<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use Searchable, GlobalStatus;

    protected $casts = [
        'features' => 'array',
        'gallery'  => 'array'
    ];

    public function images()
    {
        return $this->hasMany(ProductGallery::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function imageShow()
    {
        return getImage(getFilePath('product') . '/' . $this->image, getFileSize('product'));
    }

    // Scopes
    public function scopeTodayDeal($query)
    {
        return $query->where('today_deals', Status::YES);
    }

    public function scopeHotDeal($query)
    {
        return $query->where('hot_deals', Status::YES);
    }

    public function scopeBestSelling($query)
    {
        return $query->where('sale_count', '!=', 0)->orderBy('sale_count', 'DESC');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured_product', Status::YES);
    }

    public function scopeAvailable($query)
    {
        return $query->active()->whereHas('category', function ($category) {
            $category->active();
        })->whereHas('brand', function ($brand) {
            $brand->active();
        })->whereHas('subcategory', function ($subcategory) {
            $subcategory->active();
        });
    }
}
