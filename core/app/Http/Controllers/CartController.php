<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'quantity'   => 'required|integer|gt:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $product = Product::where('id', $request->product_id)->active()->first();

        if (!$product) {
            return response()->json(['error' => 'Product not found or something went wrong']);
        }

        $userId        = auth()->id();
        $notInStockMsz = 'Requested quantity is not available in our stock';

        if ($request->quantity > $product->quantity) {
            return response()->json(['error' => $notInStockMsz]);
        }

        if ($userId) {
            $cart = Cart::where('user_id', $userId)->where('product_id', $request->product_id)->first();

            if ($cart) {
                if ($cart->quantity >= $product->quantity) {
                    return response()->json(['error' => $notInStockMsz]);
                }

                $cart->quantity += $request->quantity;
            } else {
                $cart             = new Cart();
                $cart->user_id    = auth()->id();
                $cart->product_id = $request->product_id;
                $cart->quantity   = $request->quantity;
            }
            $cart->save();

        } else {
            $cart = session()->get('cart', []);

            if (isset($cart[$product->id])) {

                if ($cart[$product->id]['quantity'] >= $product->quantity) {
                    return response()->json(['error' => $notInStockMsz]);
                }

                $cart[$product->id]['quantity'] += $request->quantity;
            } else {
                $general            = gs();
                $cart[$product->id] = [
                    'name'          => $product->name,
                    'price'         => $product->price,
                    'discount'      => ($product->today_deals == Status::YES) ? $general->discount : $product->discount,
                    'discount_type' => ($product->today_deals == Status::YES) ? $general->discount_type : $product->discount_type,
                    'image'         => $product->image,
                    'product_id'    => $product->id,
                    'quantity'      => $request->quantity,
                ];
            }
        }

        session()->put('cart', $cart);
        return response()->json(['success' => 'Product added to shopping cart']);
    }

    public function getCartCount()
    {
        $userId = auth()->id();

        if ($userId) {
            return Cart::where('user_id', $userId)->count();
        }

        $cart = session()->get('cart');

        if ($cart) {
            return count($cart);
        }

        return 0;
    }

    public function cartProducts()
    {
        $pageTitle = 'My Cart';
        $userId    = auth()->id();
        $carts     = [];

        $cart  = session()->get('cart');
        $carts = json_decode(json_encode($cart)) ?? [];

        if ($userId) {
            $carts = Cart::where('user_id', $userId)->with('product')->orderBy('id', 'asc')->get();
        }

        session()->forget('total');
        return view($this->activeTemplate . 'cart', compact('pageTitle', 'carts'));
    }

    public function removeCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $userId = auth()->id();

        if ($userId) {
            $cart = Cart::where('user_id', $userId)->where('product_id', $request->product_id)->first();
            $cart->delete();
        } else {
            $cart = session()->get('cart');
            unset($cart[$request->product_id]);
            session()->put('cart', $cart);
        }

        return response()->json(['success' => 'Product was successfully removed.']);
    }

    public function updateCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'quantity'   => 'required|integer|gt:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $product = Product::findOrFail($request->product_id);
        $userId  = auth()->id();

        if ($request->quantity > $product->quantity) {
            return response()->json(['error' => 'Requested quantity is not available in our stock.']);
        }

        if ($userId) {
            $cart           = Cart::where('user_id', $userId)->where('product_id', $request->product_id)->first();
            $cart->quantity = $request->quantity;
            $cart->save();
        } else {
            $cart                                   = session()->get('cart');
            $cart[$request->product_id]["quantity"] = $request->quantity;
            session()->put('cart', $cart);
        }

        return response()->json(['success' => 'Cart was successfully updated.']);
    }

    public function couponApply(Request $request)
    {
        $coupon = Coupon::where('name', $request->coupon)->whereDate('start_date', '<=', now())->whereDate('end_date', '>=', now())->active()->first();

        if (!$coupon) {
            return response()->json(['error' => 'No coupon found.']);
        }

        $userId  = auth()->id();
        $general = gs();

        if ($userId) {
            $carts = Cart::where('user_id', $userId)->with('product')->get();

            foreach ($carts as $cart) {
                $sumPrice = 0;
                $product  = Product::where('id', $cart->product->id)->active()->first();
                $price    = productPrice($product);
                $sumPrice = $sumPrice + ($price * $cart->quantity);
                $total[]  = $sumPrice;
            }
        } else {
            $carts = session()->get('cart');

            foreach ($carts as $cart) {
                $sumPrice = 0;
                $product  = Product::where('id', $cart['product_id'])->active()->first();
                $price    = productPrice($product);
                $sumPrice = $sumPrice + ($price * $cart['quantity']);
                $total[]  = $sumPrice;
            }
        }

        $subtotal = array_sum($total);

        if ($coupon->min_order > $subtotal) {
            return response()->json(['error' => 'Sorry, you have to order a minimum amount of ' . $general->cur_sym . showAmount($coupon->min_order)]);
        }

        if ($coupon->discount_type == 1) {
            $discount = $coupon->discount;
        } else {
            $discount = $subtotal * $coupon->discount / 100;
        }

        $totalAmount = $subtotal - $discount;

        $total = [
            'coupon_name'   => $coupon->name,
            'coupon_id'     => $coupon->id,
            'discount_type' => $coupon->discount_type,
            'subtotal'      => $subtotal,
            'discount'      => $discount,
            'totalAmount'   => $totalAmount,
        ];

        session()->put('total', $total);

        return response()->json([
            'success'     => 'Coupon has been successfully added.',
            'subtotal'    => $subtotal,
            'discount'    => $discount,
            'totalAmount' => $totalAmount,
        ]);
    }
}
