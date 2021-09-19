<?php


namespace App\Services;


use App\Helpers\Product;
use App\Jobs\InitAppJob;
use App\Jobs\UpdatePriceEndProductJob;
use App\Mail\UninstallApp;
use App\Models\CampaignProduct;
use App\Repositories\CampaignProductRepository;
use App\Repositories\ShopRepository;
use App\ShopifyApi\RestApi\AssetApi;
use App\ShopifyApi\RestApi\BaseApi;
use App\ShopifyApi\RestApi\PageApi;
use App\ShopifyApi\RestApi\ThemeApi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Socialhead\Core\Services\ShopService;

class WebhookService
{
    private $shopService;
    public function __construct(ShopService $shopService)
    {
        $this->shopService = $shopService;
    }

    /**
     * @param $payload
     * @return bool
     */
    function shopUpdate($payload)
    {
        $id = $payload['id'];
        $this->shopService->createOrUpdate($id, $payload);
        return true;
    }

    /**
     * @param $payload
     * @return bool
     */
    function uninstallShop($payload)
    {
        $id = $payload['id'];
        $this->shopService->createOrUpdate($id, [
           'access_token' => null,
           'status' => false
        ]);

        return true;
    }

}
