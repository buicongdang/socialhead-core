<?php

namespace Socialhead\Core\Repository;

use Socialhead\Core\Models\Shop;

class ShopRepository
{
    public function createOrUpdate($id, $payload) {
        $shop = Shop::where('id', $id)->first();

        if ($shop) {
            $shop->update($payload);
            return $shop->toArray();
        }

        return Shop::create($payload)->toArray();
    }

    /**
     * @param $id
     * @return mixed
     */
    function get($id)
    {
        return Shop::find($id);
    }

    /**
     * @param $domain
     * @return mixed
     */
    function getByMyShopifyDomain($domain)
    {
        return Shop::where('myshopify_domain', $domain)->first();
    }

}
