<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $pageTitle = "Manage Product";
        $products  = $this->productData();
        return view('admin.product.index', compact('pageTitle', 'products'));
    }

    public function todayDealProduct()
    {
        $pageTitle = "Today Deal";
        $products  = $this->productData('todayDeal');
        return view('admin.product.index', compact('pageTitle', 'products'));
    }

    public function featureProduct()
    {
        $pageTitle = "Featured Products";
        $products  = $this->productData('featured');
        return view('admin.product.index', compact('pageTitle', 'products'));
    }

    public function hotProduct()
    {
        $pageTitle = "Hot Deal Products";
        $products  = $this->productData('featured');

        return view('admin.product.index', compact('pageTitle', 'products'));
    }

    protected function productData($scope = null)
    {
        if ($scope) {
            $product = Product::$scope();
        } else {
            $product = Product::query();
        }
        return $product->searchable(['name', 'product_sku', 'category:name', 'subcategory:name', 'brand:name'])->latest()->paginate(getPaginate());
    }

    public function create()
    {
        $pageTitle  = "Add New Product";
        $brands     = Brand::active()->orderBy('name')->get();
        $categories = Category::active()->with(['subcategories' => function ($q) {
            $q->active();
        }])->orderBy('name')->get();

        return view('admin.product.create', compact('pageTitle', 'brands', 'categories'));
    }

    public function edit($id)
    {
        $pageTitle  = "Edit Product";
        $product    = Product::findOrFail($id);
        $brands     = Brand::orderBy('name')->get();
        $categories = Category::with('subcategories')->orderBy('name')->get();
        $galleries  = [];

        foreach ($product->gallery ?? [] as $key => $gallery) {
            $img['id']   = $gallery;
            $img['src']  = getImage(getFilePath('productGallery') . '/' . $gallery);
            $galleries[] = $img;
        }

        return view('admin.product.edit', compact('pageTitle', 'product', 'brands', 'categories', 'galleries'));
    }

    public function store(Request $request, $id = 0)
    {
        $isRequired = $id ? 'nullable' : 'required';

        $request->validate([
            'name'                   => 'required|max:255',
            'brand_id'               => 'required|exists:brands,id',
            'category_id'            => 'required|exists:categories,id',
            'subcategory_id'         => 'required|exists:subcategories,id',
            'product_sku'            => 'required|string',
            'quantity'               => 'required|integer|gt:0',
            'price'                  => 'required|numeric|gt:0',
            'discount'               => 'nullable|numeric|min:0',
            'discount_type'          => 'required|in:1,2',
            'digital_item'           => 'required|in:0,1',
            'file_type'              => 'required_if:digital_item,1|in:1,2',
            'link'                   => 'required_if:file_type,2|url|max:255',
            'summary'                => 'required',
            'description'            => 'required',
            'features'               => 'nullable|array|min:1',
            'features.*.title'       => 'required_with:features|string',
            'features.*.description' => 'required_with:features|string',
            'image'                  => [$isRequired, 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])],
            'gallery'                => "$isRequired|array|min:1|max:6",
            'gallery.*'              => [$isRequired, 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])],
        ]);

        if (!$request->old && !$request->gallery) {
            $notify[] = ['error', 'Minimum one gallery image is required'];
            return back()->withNotify($notify);
        }

        if ($request->discount) {
            if ($request->discount_type == 1) {
                $discount = $request->price - $request->discount;
            } else {
                $discount = $request->price - (($request->price * $request->discount) / 100);
            }

            if ($discount <= 0) {
                $notify[] = ['error', 'Discount price can\'t be grater than main price'];
                return back()->withNotify($notify);
            }
        }
        $isFileRequired = 'required_if:file_type,1';
        if ($id) {
            $product = Product::findOrFail($id);

            if (($product->file_type == 2) && !$product->file && ($request->file_type == 1)) {
                $isFileRequired = 'required';
            }

            if (($product->file_type == 1) && $product->file && ($request->file_type == 1)) {
                $isFileRequired = 'nullable';
            }

            $request->validate([
                'file' => [$isFileRequired, new FileTypeValidate(['pdf', 'docx', 'txt', 'zip', 'xlx', 'csv', 'ai', 'psd', 'pptx'])],
            ]);

            $message       = "Product updated successfully";
            $imageToRemove = $request->old ? array_values(removeElement($product->gallery, $request->old)) : $product->gallery;


            if ($imageToRemove != null && count($imageToRemove)) {
                foreach ($imageToRemove as $singleImage) {
                    fileManager()->removeFile(getFilePath('productGallery') . '/' . $singleImage);
                }

                $product->gallery = removeElement($product->gallery, $imageToRemove);
            }

            if (!$request->digital_item && $product->file) {
                fileManager()->removeFile(getFilePath('productFile') . '/' . $product->file);
                $product->file = null;
            }

            if ($request->file_type == 2 && $product->file) {
                fileManager()->removeFile(getFilePath('productFile') . '/' . $product->file);
                $product->file = null;
            }
        } else {
            $request->validate([
                'file' => [$isFileRequired, new FileTypeValidate(['pdf', 'docx', 'txt', 'zip', 'xlx', 'csv', 'ai', 'psd', 'pptx'])],
            ]);

            $product = new Product();
            $message = "Product added successfully";
        }

        if ($request->hasFile('file')) {
            try {
                $product->file = fileUploader($request->file, getFilePath('productFile'), null, @$product->file);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your file'];
                return back()->withNotify($notify);
            }
        }

        if ($request->hasFile('image')) {
            try {
                $product->image = fileUploader($request->image, getFilePath('product'), getFileSize('product'), @$product->image);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $gallery = $id ? $product->gallery : [];

        if ($request->hasFile('gallery')) {
            foreach ($request->gallery as $singleImage) {
                try {
                    $gallery[] = fileUploader($singleImage, getFilePath('productGallery'), getFileSize('productGallery'));
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Couldn\'t upload your image'];
                    return back()->withNotify($notify);
                }
            }
        }

        $product->name           = $request->name;
        $product->brand_id       = $request->brand_id;
        $product->category_id    = $request->category_id;
        $product->subcategory_id = $request->subcategory_id;
        $product->product_sku    = $request->product_sku;
        $product->quantity       = $request->quantity;
        $product->price          = $request->price;
        $product->discount       = $request->discount ?? 0;
        $product->discount_type  = $request->discount_type;
        $product->digital_item   = $request->digital_item;
           $product->file_type = $request->file_type ?? 1; // Set your desired default value if file_type is NULL
        $product->link           = $request->link;
        $product->summary        = $request->summary;
        $product->description    = $request->description;
        $product->features       = $request->features;
        $product->gallery        = $gallery;
        $product->save();

        $notify[] = ["success", $message];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return Product::changeStatus($id);
    }

    public function featured($id)
    {
        return Product::changeStatus($id, 'featured_product');
    }

    public function hotDeal($id)
    {
        return Product::changeStatus($id, 'hot_deals');
    }

    public function todayDeal($id)
    {
        return Product::changeStatus($id, 'today_deals');
    }

    public function reviews($id)
    {
        $product   = Product::findOrFail($id);
        $pageTitle = 'Reviews of ' . $product->name;
        $reviews   = Review::where('product_id', $id)->with('user')->paginate(getPaginate());
        return view('admin.product.reviews', compact('pageTitle', 'reviews'));
    }

    public function reviewRemove($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        $notify[] = ['success', 'Review removed successfully'];
        return back()->withNotify($notify);
    }
}
