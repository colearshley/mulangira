<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use Searchable, GlobalStatus;

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class)->active();
    }

    public function product()
    {
        return $this->hasMany(Product::class);
    }

    public function imageShow()
    {
        return getImage(getFilePath('category') . '/' . $this->image, getFileSize('category'));
    }

    //Scope

    public function scopeFeatured($query)
    {
        return $query->where('featured', Status::YES);
    }

    public function scopeAvailable($query)
    {
        return $query->active()->whereHas('product', function ($product) {
            $product->active()->whereHas('brand', function ($brand) {
                $brand->active();
            })->whereHas('subcategory', function ($subcategory) {
                $subcategory->active();
            });
        });
    }
}
