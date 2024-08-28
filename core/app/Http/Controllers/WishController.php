<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WishController extends Controller
{

	public function addWishList(Request $request)
	{

		$validator = Validator::make($request->all(), [
			'product_id' => 'required|integer',
		]);

		if ($validator->fails()) {
			return response()->json(['error' => $validator->errors()]);
		}

		$userId = auth()->id();

		$product = Product::active()->findOrFail($request->product_id);

		if ($userId) {

			$wishlist = Wishlist::where('user_id', $userId)->where('product_id', $product->id)->first();

			if ($wishlist) {
				return response()->json(['error' => 'Already exists in wishlist']);
			}

			$wishlist             = new Wishlist();
			$wishlist->user_id    = $userId;
			$wishlist->product_id = $product->id;
			$wishlist->save();
		} else {

			$wishlist = session()->get('wishlist');

			if (isset($wishlist[$product->id])) {
				return response()->json(['error' => 'Already exists in wishlist']);
			} else {

				$wishlist[$product->id] = [
					"product_id" => $product->id,
				];
			}

			session()->put('wishlist', $wishlist);
		}

		return response()->json(['success' => 'Product added in wishlist']);
	}

	public function wishListCount()
	{

		$userId   = auth()->id();
		$data     = session()->get('wishlist');
		$wishlist = json_decode(json_encode($data));

		if ($userId) {
			$wishlist = Wishlist::where('user_id', $userId)->select('product_id')->get();
		}

		return $wishlist;
	}

	public function wishListProduct()
	{
		$pageTitle = 'My Wishlist';
		$userId    = auth()->id();
		$wishlists = [];
		$products  = [];


		if ($userId) {
			$wishlists = Wishlist::where('user_id', $userId)->select('product_id')->get();
		} else {
			$wishlists = session()->get('wishlist');
		}

		if ($wishlists) {
			$products = Product::active()->whereIn('id', $wishlists)->with('reviews')->get();
		}

		return view($this->activeTemplate . 'wishlist', compact('pageTitle', 'products'));
	}



	public function removeWishlist(Request $request)
	{

		$validator = Validator::make($request->all(), [
			'product_id' => 'required|integer',
		]);

		if ($validator->fails()) {
			return response()->json(['error' => $validator->errors()]);
		}

		$userId = auth()->id();

		if ($userId) {
			$wishlist = Wishlist::where('product_id', $request->product_id)->where('user_id', $userId)->first();
			$wishlist->delete();
		} else {
			$wishlists = session()->get('wishlist');
			unset($wishlists[$request->product_id]);
			session()->put('wishlist', $wishlists);
		}

		return response()->json(['success' => 'Product remove from wishlist']);
	}
}