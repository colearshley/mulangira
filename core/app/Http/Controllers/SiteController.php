<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\AdminNotification;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Frontend;
use App\Models\Language;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\Subscriber;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;

class SiteController extends Controller
{
    public function index()
    {
        $pageTitle         = 'Home';
        $todayDealProducts = Product::available()->todayDeal()->latest()->take(8)->get();
        return view($this->activeTemplate . 'home', compact('pageTitle', 'todayDealProducts'));
    }

    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:subscribers,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $emailExist = Subscriber::where('email', $request->email)->first();

        if (!$emailExist) {
            $subscribe        = new Subscriber();
            $subscribe->email = $request->email;
            $subscribe->save();
            return response()->json(['success' => 'Subscribed Successfully']);
        } else {
            return response()->json(['error' => 'Already Subscribed']);
        }
    }

    public function trackOrder()
    {
        $pageTitle = "Track Your Order";
        return view($this->activeTemplate . 'track.track_order', compact('pageTitle'));
    }

    public function getTrackOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orderNo' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $order = Order::where('order_no', $request->orderNo)->first();
        if (!$order) {
            return response()->json(['error' => 'Sorry! The order number was not found.']);
        }
        $emptyMessage = 'Your order has been cancelled.';
        return view($this->activeTemplate . 'track.show_track', compact('order', 'emptyMessage'));
    }

    public function productDetails($slug, $id)
    {
        $product        = Product::available()->with('category', 'reviews', 'reviews.user')->findOrFail($id);
        $reviews        = Review::where('product_id', $product->id)->latest()->limit(6)->with('user')->get();
        $pageTitle      = $product->name;
        $relatedProduct = Product::active()->with('category', 'reviews')->where('id', '!=', $id)->where('category_id', $product->category_id)->take(4)->get();
        $topProducts    = Product::active()->where('sale_count', '!=', 0)->orderBy('sale_count', 'desc')->latest()->with('reviews')->take(8)->get();

        $seoContents['keywords']           = $product->meta_keywords ?? [];
        $seoContents['social_title']       = $product->name;
        $seoContents['social_description'] = $product->summary;
        $seoContents['description']        = $product->summary;
        $seoContents['image']              = $product->imageShow();
        $seoContents['image_size']         = getFileSize('product');

        return view($this->activeTemplate . 'products.details', compact('product', 'pageTitle', 'topProducts', 'relatedProduct', 'reviews', 'seoContents'));
    }

    public function fetchReviews(Request $request, $id)
    {
        $validate = Validator::make($request->all(),[
            'skip' => 'required|integer|gt:0'
        ]);

        if($validate->fails()){
            return response()->json(['error' => $validate->errors()->all()]);
        }

        $product = Product::where('id', $id)->first();

        if (!$product) {
            return response()->json(['error' => 'Product not found']);
        }

        $reviews = Review::where('product_id', $product->id)->latest()->skip($request->skip)->limit(5)->with('user')->get();

        if (count($reviews)) {
            $view = view($this->activeTemplate . 'products.load_reviews', compact('reviews'))->render();

            return response()->json([
                'success' => true,
                'html'    => $view
            ]);
        } else {
            return response()->json([
                'error' => 'No more reviews to show'
            ]);
        }
    }

    public function contact()
    {
        $pageTitle = "Contact Us";
        $user = auth()->user();

        return view($this->activeTemplate . 'contact',compact('pageTitle','user'));
    }

    public function contactSubmit(Request $request)
    {
        $this->validate($request, [
            'name'    => 'required',
            'email'   => 'required',
            'subject' => 'required|string|max:255',
            'message' => 'required',
        ]);

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        $request->session()->regenerateToken();
        $random = getNumber();

        $ticket           = new SupportTicket();
        $ticket->user_id  = auth()->id() ?? 0;
        $ticket->name     = $request->name;
        $ticket->email    = $request->email;
        $ticket->priority = Status::PRIORITY_MEDIUM;

        $ticket->ticket     = $random;
        $ticket->subject    = $request->subject;
        $ticket->last_reply = Carbon::now();
        $ticket->status     = Status::TICKET_OPEN;
        $ticket->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = auth()->user() ? auth()->user()->id : 0;
        $adminNotification->title     = 'A new contact message has been submitted';
        $adminNotification->click_url = urlPath('admin.ticket.view', $ticket->id);
        $adminNotification->save();

        $message                    = new SupportMessage();
        $message->support_ticket_id = $ticket->id;
        $message->message           = $request->message;
        $message->save();

        $notify[] = ['success', 'Ticket created successfully!'];

        return to_route('ticket.view', [$ticket->ticket])->withNotify($notify);
    }

    public function policyPages($slug, $id)
    {
        $policy    = Frontend::where('id', $id)->where('data_keys', 'policy_pages.element')->firstOrFail();
        $pageTitle = $policy->data_values->title;
        return view($this->activeTemplate . 'policy', compact('policy', 'pageTitle'));
    }

    public function changeLanguage($lang = null)
    {
        $language = Language::where('code', $lang)->first();

        if (!$language) {
            $lang = 'en';
        }

        session()->put('lang', $lang);
        return back();
    }

    public function cookieAccept()
    {
        Cookie::queue('gdpr_cookie',gs('site_name') , 43200);
    }

    public function cookiePolicy()
    {
        $pageTitle = 'Cookie Policy';
        $cookie    = Frontend::where('data_keys', 'cookie.data')->first();
        return view($this->activeTemplate . 'cookie', compact('pageTitle', 'cookie'));
    }

    public function placeholderImage($size = null)
    {
        $imgWidth  = explode('x', $size)[0];
        $imgHeight = explode('x', $size)[1];
        $text      = $imgWidth . '×' . $imgHeight;
        $fontFile  = realpath('assets/font/RobotoMono-Regular.ttf');
        $fontSize  = round(($imgWidth - 50) / 8);

        if ($fontSize <= 9) {
            $fontSize = 9;
        }

        if ($imgHeight < 100 && $fontSize > 30) {
            $fontSize = 30;
        }

        $image     = imagecreatetruecolor($imgWidth, $imgHeight);
        $colorFill = imagecolorallocate($image, 100, 100, 100);
        $bgFill    = imagecolorallocate($image, 175, 175, 175);
        imagefill($image, 0, 0, $bgFill);
        $textBox    = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth  = abs($textBox[4] - $textBox[0]);
        $textHeight = abs($textBox[5] - $textBox[1]);
        $textX      = ($imgWidth - $textWidth) / 2;
        $textY      = ($imgHeight + $textHeight) / 2;
        header('Content-Type: image/jpeg');
        imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);
        imagejpeg($image);
        imagedestroy($image);
    }

    public function maintenance()
    {
        $pageTitle = 'Maintenance Mode';
        $general   = gs();

        if(gs('maintenance_mode') == Status::DISABLE){
            return to_route('home');
        }

        $maintenance = Frontend::where('data_keys', 'maintenance.data')->first();
        return view($this->activeTemplate . 'maintenance', compact('pageTitle', 'maintenance'));
    }

    public function products()
    {
        $pageTitle = "All Products";
        $data      = $this->getProductData();
        $products  = $data['products']->paginate(getPaginate());
        return view($this->activeTemplate . 'products.index', compact('pageTitle', 'products', 'data'));
    }

    public function hotDeal()
    {
        $pageTitle = "Hot Deal Product";
        $data      = $this->getProductData('hotDeal');
        $products  = $data['products']->paginate(getPaginate());
        return view($this->activeTemplate . 'products.index', compact('pageTitle', 'products', 'data'));
    }

    public function featured()
    {
        $pageTitle = "Featured Product";
        $data      = $this->getProductData('featured');
        $products  = $data['products']->paginate(getPaginate());
        return view($this->activeTemplate . 'products.index', compact('pageTitle', 'products', 'data'));
    }

    public function bestSelling()
    {
        $pageTitle = "Best Selling Product";
        $data      = $this->getProductData('bestSelling');
        $products  = $data['products']->paginate(getPaginate());
        return view($this->activeTemplate . 'products.index', compact('pageTitle', 'products', 'data'));
    }

    public  function categoryAll()
    {
        $pageTitle  = "All Categories";
        $categories = Category::active()->orderBy('name')->paginate(getPaginate());
        return view($this->activeTemplate . 'all_category', compact('pageTitle', 'categories'));
    }

    public function allBrand()
    {
        $pageTitle = 'All Brands';
        $brands    = Brand::active()->orderBy('name')->paginate(getPaginate());
        return view($this->activeTemplate . 'all_brands', compact('pageTitle', 'brands'));
    }

    public function categoryProduct($slug, $id)
    {
        $pageTitle = keyToTitle($slug) . '- Products';
        $data      = $this->getProductData();
        $products  = $data['products']->where('category_id', $id)->latest()->paginate(getPaginate());
        return view($this->activeTemplate . 'products.index', compact('pageTitle', 'products', 'data'));
    }

    public function brandProduct($slug, $id)
    {
        $pageTitle = keyToTitle($slug) . '- Products';
        $data = $this->getProductData();
        $products = $data['products']->where('brand_id', $id)->latest()->paginate(getPaginate());
        return view($this->activeTemplate . 'products.index', compact('pageTitle', 'products', 'data'));
    }

    public function subCategoryProduct($slug, $id)
    {
        $pageTitle = keyToTitle($slug) . '- Products';
        $data = $this->getProductData();
        $products = $data['products']->where('subcategory_id', $id)->latest()->paginate(getPaginate());
        return view($this->activeTemplate . 'products.index', compact('pageTitle', 'products', 'data'));
    }


    public function filterProduct(Request $request)
    {
        $productList = $this->getProductData()['products'];

        if ($request->brandId) {
            $productList = $productList->where('brand_id', $request->brandId);
        }

        if ($request->categoryId) {
            if ($request->categoryId != 0) {
                $productList   = $productList->where('category_id', $request->categoryId);
                $productFilter = $this->subcategoriesQuery($productList, $request);
            }
        } else {
            $productFilter = $this->categoriesQuery($productList, $request);
        }

        if ($request->subcategoryId) {
            $productFilter = $productList->where('subcategory_id', $request->subcategoryId);
        }

        $productFilter = $this->productsQuery($productFilter, $request);

        if ($request->paginate == null) {
            $paginate = getPaginate();
        } else {
            $paginate = $request->paginate;
        }

        $products     = $productFilter->latest()->paginate($paginate);
        return view($this->activeTemplate . 'products.show_products', compact('products'));
    }

    protected function categoriesQuery($productList, $request)
    {
        if ($request->categories) {
            $productList = $productList->whereIn('category_id', $request->categories);
        }

        return $productList;
    }

    protected function subcategoriesQuery($productList, $request)
    {
        if ($request->subcategories) {
            $productList = $productList->whereIn('subcategory_id', $request->subcategories);
        }

        return $productList;
    }

    protected function productsQuery($productFilter, $request)
    {
        if ($request->brands) {
            $productFilter = $productFilter->whereIn('brand_id', $request->brands);
        }

        if ($request->min && $request->max) {
            $productFilter = $productFilter->whereBetween('price', [$request->min, $request->max]);
        }

        if ($request->sort) {
            $sort          = explode('_', $request->sort);
            $productFilter = $productFilter->orderBy(@$sort[0], @$sort[1]);
        }

        return $productFilter;
    }

    public function quickView(Request $request)
    {
        $product = Product::active()->with('reviews')->findOrFail($request->product_id);
        return view($this->activeTemplate . 'products.quick_view', compact('product'));
    }

    protected function getProductData($scope = null)
    {
        if ($scope) {
            $products = Product::$scope();
        } else {
            $products = Product::query();
        }

        $products = $products->available()->searchable(['name', 'description', 'features', 'summary', 'category:name', 'subcategory:name', 'brand:name']);

        $minPrice = $products->min('price');
        $maxPrice = $products->max('price');

        $categoryList = Category::active()->whereHas('product', function ($product) {
            $product->active();
        })->get();
        $brands = Brand::active()->whereHas('product', function ($product) {
            $product->active();
        })->get();

        return [
            'products'     => $products,
            'minPrice'     => $minPrice ?? 0,
            'maxPrice'     => $maxPrice ?? 0,
            'brands'       => $brands,
            'categoryList' => $categoryList,
        ];
    }

    public function download($id, $fileName)
    {
        Product::where('id', $id)->where('digital_item', Status::YES)->where('file_type', 1)->where('file', $fileName)->firstOrFail();
        $path    = fileManager()->productFile()->path.'/'.$fileName;
        return response()->download($path);
    }
}
