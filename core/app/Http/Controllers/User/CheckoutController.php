<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use App\Traits\OrderConfirmation;

class CheckoutController extends Controller
{
    use OrderConfirmation;

	public function checkout(Request $request)
	{
		$pageTitle = 'Checkout';
		$total     = session()->get('total');
		$userId    = auth()->user()->id;

		if ($total) {
			$data['subtotal'] = $total['subtotal'];
			$data['discount'] = $total['discount'];
			$data['total']    = $total['totalAmount'];
		} else {
			$subtotal = $this->cartSubTotal($userId);

			if ($subtotal == 0) { abort(404); }

			$data['subtotal'] = $subtotal;
			$data['discount'] = showAmount(0.00);
			$data['total']    = $subtotal;
		}

		$countries      = json_decode(file_get_contents(resource_path('views/partials/country.json')));
		$shippingMethod = ShippingMethod::active()->get();
        $user           = auth()->user();

		return view($this->activeTemplate . 'checkout', compact('pageTitle', 'data', 'countries', 'shippingMethod', 'user'));
	}

	public function order(Request $request)
	{
		$request->validate([
			'firstname'       => 'required',
			'lastname'        => 'required',
			'mobile'          => 'required',
			'email'           => 'required',
			'country'         => 'required',
			'address'         => 'required',
			'state'           => 'required',
			'city'            => 'required',
			'zip'             => 'required',
			'shipping_method' => 'required|integer',
			'payment_type'    => 'required|integer|in:1,2',
		]);

		$user     = auth()->user();
		$subtotal = $this->cartSubTotal($user->id);
		$shipping = ShippingMethod::where('id', $request->shipping_method)->where('status', Status::ENABLE)->first();

		if (!$shipping) {
			$notify[] = ['error', 'Shipping method unable to locate.'];
			return back()->withNotify($notify)->withInput();
		}

        $grandTotal = $subtotal + $shipping->price;
		$total = session()->get('total');

		if ($total) {
			$discount   = $total['discount'];
			$coupon_id  = $total['coupon_id'];
			$grandTotal = $grandTotal - $discount;
		}

		$address = [
			'address' => $request->address,
			'state'   => $request->state,
			'zip'     => $request->zip,
			'country' => $request->country,
			'city'    => $request->city,
		];

		$order                     = new Order();
		$order->user_id            = $user->id;
		$order->order_no           = getTrx();
		$order->subtotal           = $subtotal;
		$order->discount           = $discount ?? 0;
		$order->shipping_charge    = $shipping->price;
		$order->total              = $grandTotal;
		$order->coupon_id          = $coupon_id ?? 0;
		$order->shipping_method_id = $shipping->id;
		$order->address            = json_encode($address);
		$order->payment_type       = $request->payment_type;
        $order->save();

        if ($request->payment_type == Status::PAYMENT_ONLINE) {
            return redirect()->route('user.deposit.index', $order->id);
        }

        static::confirmOrder($order);

        $notify[] = ['success', 'Order successfully completed.'];
        return redirect()->route('user.order.index')->withNotify($notify);
	}

	protected function cartSubTotal($user_id)
	{
		$carts = Cart::where('user_id', $user_id)->with('product')->get();
		$total = [0];

		foreach ($carts as $cart) {
			$sumPrice = 0;
			$product  = Product::active()->where('id', $cart->product->id)->first();
			$price    = productPrice($product);

			$sumPrice = $sumPrice + ($price * $cart->quantity);
			$total[]  = $sumPrice;
		}

		$subtotal = array_sum($total);
		return $subtotal;
	}
}
