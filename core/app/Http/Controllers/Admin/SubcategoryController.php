<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
	public function index()
	{
		$pageTitle     = "Manage Subcategory";
		$categories    = Category::get();
		$subcategories = Subcategory::searchable(['name', 'category:name'])->with('category')->paginate(getPaginate());
		return view('admin.sub_category', compact('pageTitle', 'subcategories', 'categories'));
	}

	public function store(Request $request, $id = null)
	{
		$request->validate([
			'name'        => 'required',
			'category_id' => 'required|integer|exists:categories,id',
		]);

		if ($id) {
			$subcategory = Subcategory::findOrFail($id);
			$message     = "Subcategory updated successfully";
		} else {
			$subcategory = new Subcategory();
			$message     = "Subcategory created successfully";
		}

		$subcategory->name        = $request->name;
		$subcategory->category_id = $request->category_id;
		$subcategory->save();

		$notify[] = ["success", $message];
		return back()->withNotify($notify);
	}

	public function status($id)
	{
		return Subcategory::changeStatus($id);
	}
}
