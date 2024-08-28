<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $pageTitle = "Review products";

        $productId = OrderDetail::with('order')
            ->whereHas('order', function ($order) {
                $order->where('user_id', auth()->id())->delivered();
            })
            ->distinct('product_id')
            ->pluck('product_id');

        $products = Product::active()
            ->whereIn('id', $productId)
            ->with(['reviews' => function ($q) {
                $q->where('user_id', auth()->id());
            }])
            ->paginate(getPaginate());

        return view($this->activeTemplate . 'user.review.index', compact('pageTitle', 'products'));
    }

    public function create($slug, $id)
    {
        $pageTitle = 'Add Review';
        $product = Product::active()->findOrFail($id);
        return view($this->activeTemplate . 'user.review.create', compact('pageTitle', 'product'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'stars' => 'required|integer|in:1,2,3,4,5',
            'review_comment' => 'required|string'
        ]);

        $existingReview = Review::where('user_id', auth()->id())->where('product_id', $id)->exists();

        if ($existingReview) {
            $notify[] = ['error', 'You have already reviewed this product.'];
            return back()->withNotify($notify);
        }

        $product = Product::with('reviews')->findOrFail($id);

        $newReview = new Review();
        $newReview->user_id = auth()->id();
        $newReview->product_id = $product->id;
        $newReview->stars = $request->stars;
        $newReview->review_comment = $request->review_comment;
        $newReview->save();

        $totalReview = $product->reviews->count();
        $totalStar = $product->reviews->sum('stars');
        $avgRating = $totalReview > 0 ? $totalStar / $totalReview : 0;

        $product->avg_rate = $avgRating;
        $product->save();

        $notify[] = ['success', 'Review added successfully.'];
        return back()->withNotify($notify);
    }
}
