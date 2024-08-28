<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Traits\OrderConfirmation;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use OrderConfirmation;

	public function index()
	{
		$pageTitle = "All Orders";
		$orders    = $this->orderData();
		return view('admin.order.index', compact('pageTitle', 'orders'));
	}

	public function pending()
	{
		$pageTitle = "Pending Orders";
		$orders    = $this->orderData('pending');
		return view('admin.order.index', compact('pageTitle', 'orders'));
	}

	public function confirmed()
	{
		$pageTitle = "Confirmed Orders";
		$orders    = $this->orderData('confirmed');
		return view('admin.order.index', compact('pageTitle', 'orders'));
	}

	public function shipped()
	{
		$pageTitle = "Shipped Orders";
		$orders    = $this->orderData('shipped');
		return view('admin.order.index', compact('pageTitle', 'orders'));
	}

	public function delivered()
	{
		$pageTitle = "Delivered Orders";
		$orders    = $this->orderData('delivered');
		return view('admin.order.index', compact('pageTitle', 'orders'));
	}

	public function cancel()
	{
		$pageTitle = "Cancel Orders";
		$orders    = $this->orderData('cancel');
		return view('admin.order.index', compact('pageTitle', 'orders'));
	}

	private function orderData($scope = null)
	{
		if ($scope) {
			$orders = Order::$scope();
		} else {
			$orders = Order::query();
		}

		return $orders->searchable(['order_no', 'user:username'])->with('user')->latest()->paginate(getPaginate());
	}

	public function details($id)
	{
		$pageTitle = 'Order Detail';
		$order     = Order::where('id', $id)->with(['orderDetail.product', 'coupon', 'shipping', 'deposit', 'user', 'orderDetail'])->firstOrFail();
		return view('admin.order.detail', compact('pageTitle', 'order'));
	}

	public function invoice($id)
	{
		$pageTitle = 'Print Invoice';
		$order     = Order::where('id', $id)->with(['orderDetail.product', 'coupon', 'shipping', 'deposit', 'user', 'orderDetail'])->firstOrFail();
		return view('admin.order.invoice', compact('order'));
	}

	public function status(Request $request, $id)
	{
		$request->validate([
			'order_status' => 'required|integer',
		]);

		$order               = Order::where('id', $id)->with('user', 'orderDetail')->firstOrFail();
		$order->order_status = $request->order_status;
		$user                = $order->user;

		if ($request->order_status == Status::ORDER_CONFIRMED) {
			$status = 'Confirmed';
		} elseif ($request->order_status == Status::ORDER_SHIPPED) {
			$status = 'Shipped';
		} elseif ($request->order_status == Status::ORDER_DELIVERED) {
			$status = 'Delivered';

			if ($order->payment_type == Status::PAYMENT_OFFLINE) {
				$order->payment_status = Status::ORDER_PAYMENT_SUCCESS;
			}

            $productSellUpdate = [];

            foreach ($order->orderDetail as $detail) {
                $productSellUpdate[$detail->product_id] = $detail->quantity;
            }

            if (!empty($productSellUpdate)) {
                $updateValues = [];

                foreach ($productSellUpdate as $id => $quantity) {
                    $updateValues[] = "WHEN id = $id THEN sale_count + $quantity";
                }

                $updateValues = implode(' ', $updateValues);
                Product::whereIn('id', array_keys($productSellUpdate))->update([
                    'sale_count' => DB::raw("CASE $updateValues ELSE sale_count END")
                ]);
            }
		} else {
			$status = 'Cancelled';
            static::orderCancel($order);
		}

		$order->save();

		notify($user, 'ORDER_STATUS', [
			'method_name' => 'Your order has now ' . $status,
			'user_name'   => $user->username,
			'order_no'    => $order->order_no,
			'total'       => showAmount($order->total),
		]);

		$notify[] = ['success', 'Order status change successfully.'];
		return back()->withNotify($notify);
	}
}
