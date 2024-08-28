<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;

class ShippingMethodController extends Controller
{
	public function index()
	{
		$pageTitle = "Manage Shipping Method";
		$shippings = ShippingMethod::searchable(['name'])->paginate(getPaginate());
		return view('admin.shipping', compact('pageTitle', 'shippings'));
	}

	public function store(Request $request, $id = 0)
	{
		$request->validate([
			'name'  => 'required|unique:shipping_methods,name,' . $id,
			'price' => 'required|numeric|min:0',
		]);

        if ($id) {
			$shipping = ShippingMethod::findOrFail($id);
			$message  = "Shipping method updated successfully";
		} else {
			$shipping = new ShippingMethod();
			$message  = "Shipping method added successfully";
		}

        $shipping->name  = $request->name;
		$shipping->price = $request->price;
		$shipping->save();

		$notify[] = ["success", $message];
		return back()->withNotify($notify);
	}

	public function status($id)
	{
		return ShippingMethod::changeStatus($id);
	}
}
