<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use GlobalStatus, Searchable;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function shipping()
    {
        return $this->belongsTo(ShippingMethod::class, 'shipping_method_id');
    }

    public function deposit()
    {
        return $this->hasOne(Deposit::class, 'order_id');
    }

    public function orderDetail()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    public function scopePending()
    {
        return $this->where('order_status', Status::ORDER_PENDING)->where(function($qOne) {
                    $qOne->where('payment_type', Status::PAYMENT_OFFLINE)->orWhere(function ($qTwo) {
                        $qTwo->where('payment_type', Status::PAYMENT_ONLINE)->where('payment_status', Status::ORDER_PAYMENT_SUCCESS);
                    });
                });
    }

    public function scopeConfirmed()
    {
        return $this->where('order_status', Status::ORDER_CONFIRMED);
    }

    public function scopeShipped()
    {
        return $this->where('order_status', Status::ORDER_SHIPPED);
    }

    public function scopeDelivered()
    {
        return $this->where('order_status', Status::ORDER_DELIVERED);
    }

    public function scopeCancel()
    {
        return $this->where('order_status', Status::ORDER_CANCEL);
    }

    public function ordersBadge(): Attribute
    {
        return new Attribute(function () {
            $html = '';

            if ($this->order_status == Status::ORDER_PENDING) {
                $html = '<span class="badge badge--warning">' . trans('Pending') . '</span>';
            } elseif ($this->order_status == Status::ORDER_CONFIRMED) {
                $html = '<span class="badge badge--success">' . trans('Confirmed') . '</span>';
            } elseif ($this->order_status == Status::ORDER_DELIVERED) {
                $html = '<span class="badge badge--primary">' . trans('Delivered') . '</span>';
            } elseif ($this->order_status == Status::ORDER_SHIPPED) {
                $html = '<span class="badge badge--dark">' . trans('Shipped') . '</span>';
            } else {
                $html = '<span class="badge badge--danger">' . trans('Rejected') . '</span>';
            }

            return $html;
        });
    }

    public function paymentBadge(): Attribute
    {
        return new Attribute(function () {
            $html = '';
            if ($this->payment_status == Status::ORDER_PAYMENT_PENDING) {
                $html = '<span class="badge badge--warning">' . trans('Pending') . '</span>';
            } elseif ($this->payment_status == Status::ORDER_PAYMENT_SUCCESS) {
                $html = '<span class="badge badge--success">' . trans('Success') . '</span>';
            } else {
                $html = '<span class="badge badge--danger">' . trans('Cancel') . '</span>';
            }

            return $html;
        });
    }
}
