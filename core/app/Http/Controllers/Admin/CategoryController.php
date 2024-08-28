<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
	public function index()
	{
		$pageTitle  = "All Categories";
		$categories = Category::searchable(['name'])->paginate(getPaginate());
		return view('admin.category', compact('pageTitle', 'categories'));
	}

	public function store(Request $request, $id = 0)
	{

		$isRequired = $id ? 'nullable' : 'required';

		$request->validate([
			'name'  => 'required|max:255|unique:categories,name,' . $id,
			'image' => [ 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
		]);

		if ($id) {
			$category = Category::findOrFail($id);
			$message  = "Category update successfully";
		} else {
			$category = new Category();
			$message  = "Category added successfully";
		}

		if ($request->hasFile('image')) {
			try {
				$category->image = fileUploader($request->image, getFilePath('category'), getFileSize('category'), @$category->image);
			} catch (\Exception $exp) {
				$notify[] = ['error', 'Couldn\'t upload your image'];
				return back()->withNotify($notify);
			}
		}

		$category->name = $request->name;
		$category->save();

		$notify[] = ["success", $message];
		return back()->withNotify($notify);
	}

	public function status($id)
	{
		return Category::changeStatus($id);
	}

	public function featured($id)
	{
		return Category::changeStatus($id, 'featured');
	}
}
