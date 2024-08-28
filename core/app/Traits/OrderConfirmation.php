<?php

namespace App\Traits;

use App\Constants\Status;
use App\Models\AdminNotification;
use App\Models\Cart;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

trait OrderConfirmation
{
    public static function confirmOrder($order) {
        $user    = auth()->user();
        $carts   = Cart::where('user_id', $user->id)->get();
        $general = gs();

        $orderDetailsData = [];
        $productStockUpdate = [];

        foreach ($carts as $cart) {
            $orderDetailsData[] = [
                'order_id'   => $order->id,
                'product_id' => $cart->product_id,
                'quantity'   => $cart->quantity,
                'price'      => productPrice($cart->product),
            ];

            $productStockUpdate[$cart->product_id] = $cart->quantity;
        }

        if (!empty($orderDetailsData)) {
            OrderDetail::insert($orderDetailsData);
        }

        $carts->toQuery()->delete();

        if (!empty($productStockUpdate)) {
            $updateValues = [];

            foreach ($productStockUpdate as $id => $quantity) {
                $updateValues[] = "WHEN id = $id THEN quantity - $quantity";
            }

            $updateValues = implode(' ', $updateValues);
            Product::whereIn('id', array_keys($productStockUpdate))->update([
                'quantity' => DB::raw("CASE $updateValues ELSE quantity END")
            ]);
        }

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id;
        $adminNotification->title     = 'Order successfully placed.';
        $adminNotification->click_url = urlPath('admin.orders.detail',$order->id);
        $adminNotification->save();

        notify($user, 'ORDER_COMPLETE', [
            'method_name'     => 'Order successfully placed.',
            'user_name'       => $user->username,
            'subtotal'        => showAmount($order->subtotal),
            'shipping_charge' => showAmount($order->shipping_charge),
            'total'           => showAmount($order->total),
            'currency'        => $general->cur_text,
            'order_no'        => $order->order_no,
        ]);
    }

    protected static function transactionCreate($order, $user, $deposit) {
        $order->payment_status = Status::ORDER_PAYMENT_SUCCESS;
        $order->save();

        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->amount       = $order->total;
        $transaction->post_balance = 0;
        $transaction->charge       = $deposit->charge;
        $transaction->trx_type     = '-';
        $transaction->details      = 'Order confirmation via '.$deposit->gatewayCurrency()->name;
        $transaction->trx          = $order->order_no;
        $transaction->remark       = 'Payment';
        $transaction->save();
    }

    protected static function orderCancel($order) {
        $order->payment_status = Status::ORDER_PAYMENT_CANCEL;
        $order->save();

        $productStockUpdate = [];

        foreach ($order->orderDetail as $detail) {
            $productStockUpdate[$detail->product_id] = $detail->quantity;
        }

        if (!empty($productStockUpdate)) {
            $updateValues = [];

            foreach ($productStockUpdate as $id => $quantity) {
                $updateValues[] = "WHEN id = $id THEN quantity + $quantity";
            }

            $updateValues = implode(' ', $updateValues);
            Product::whereIn('id', array_keys($productStockUpdate))->update([
                'quantity' => DB::raw("CASE $updateValues ELSE quantity END")
            ]);
        }
    }

    protected static function createCart($user) {
        $carts = session()->get('cart');

        foreach ($carts as $key => $cart) {
            $createCart = new Cart();
            $createCart->user_id = $user->id;
            $createCart->product_id = $key;
            $createCart->quantity = $cart['quantity'];
            $createCart->save();
        }
    }
}
