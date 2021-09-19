<?php

namespace Socialhead\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'myshopify_domain',
        'domain',
        'email',
        'name',
        'country_code',
        'currency',
        'iana_timezone',
        'country',
        'phone',
        'shop_owner',
        'money_format',
        'money_with_currency_format',
        'weight_unit',
        'plan_name',
        'password_enabled',
        'has_storefront',
        'is_valid',
        'access_token',
        'status',
        'on_boarding'
    ];


    protected $appends = [
        'crisp_id'
    ];

    /**
     * @return string
     */
    function getCrispIdAttribute()
    {
        $shop = md5($this->attributes['myshopify_domain'].config('app.id'));
        $shop_id = md5($this->attributes['id']);
        $verify_secret = config('socialhead.services.crisp.verify_secret');
        return md5($shop.$verify_secret.$shop_id);
    }

}
