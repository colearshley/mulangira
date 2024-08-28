<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
	public function index()
	{
		$pageTitle = "Manage Coupon";
		$coupons   = Coupon::searchable(['name'])->paginate(getPaginate());
		return view('admin.coupon', compact('coupons', 'pageTitle'));
	}

	public function store(Request $request, $id = null)
	{
        $startDateValidation = $id ? 'required|date|date_format:Y-m-d' : 'required|date|date_format:Y-m-d|after_or_equal:today';

		$request->validate([
			'name'          => 'required|alpha_dash|max:255|unique:coupons,name,' . $id,
			'discount'      => 'required|numeric|gt:0',
			'start_date'    => $startDateValidation,
			'end_date'      => 'required|date|date_format:Y-m-d|after_or_equal:start_date',
			'discount_type' => 'required|in:1,2',
			'min_order'     => 'required|numeric|gte:0',
		]);

		if ($id) {
			$coupon  = Coupon::findOrFail($id);
			$message = "Coupon updated successfully";
		} else {
			$coupon  = new Coupon();
			$message = "Coupon added successfully";
		}

		$coupon->name          = $request->name;
		$coupon->discount      = $request->discount;
		$coupon->start_date    = $request->start_date;
		$coupon->end_date      = $request->end_date;
		$coupon->discount_type = $request->discount_type;
		$coupon->min_order     = $request->min_order;
		$coupon->save();

		$notify[] = ['success', $message];
		return back()->withNotify($notify);
	}

    public function status($id)
	{
		return Coupon::changeStatus($id);
	}
}
