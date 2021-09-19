<?php


namespace Socialhead\Core\Http\Controllers;


use App\Helpers\Common;
use App\Services\WebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Validator;
use Socialhead\Core\Events\AfterAuthAppEvent;
use Socialhead\Core\Events\BeforeAuthAppEvent;
use Socialhead\Core\Events\WebhooksEvent;
use Socialhead\Core\Helper\Socialhead;
use Socialhead\Core\Repository\ShopRepository;
use Socialhead\Core\Services\ShopService;
use Socialhead\Core\Shopify\AuthSDK;
use Socialhead\Core\Shopify\RestSDK;

class ShopifyController extends \App\Http\Controllers\Controller
{
    private $shopService;
    public function __construct(ShopService $shopService)
    {
        $this->shopService = $shopService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function generateUrl(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'shop' => 'required',
        ]);

        if ($valid->fails()) {
            return response()->json(['message' => $valid->errors()]);
        }

        $shop = $request->input('shop');
        return response()->json(['status' => true, 'data' => AuthSDK::generateUrl($shop)]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function authHandle(Request $request)
    {
//        try {
            event(new BeforeAuthAppEvent($request->all()));
            $verify_request = AuthSDK::verifyRequest($request->all());
            if (!$verify_request) {
                return redirect()->to(config('app.url').'/auth/fail?error=verify_token_invalid');
            }

            $shop         = $request->input('shop');
            $code         = $request->input('code');

            // Get access token
            $accessToken = AuthSDK::getAccessToken($shop, $code);
            //get shop api info
            $shopApi = RestSDK::config([
                'myshopify_domain' => $shop,
                'access_token' => $accessToken['access_token']
            ]);
            $shopApi  = $shopApi->Shops()->lists();
            $shopApi                 = $shopApi['data']['shop'];
            $shopApi['access_token'] = $accessToken['access_token'];
            $shopApi['status']       = true;
            //check shop is install database
            $shop = $this->shopService->get($shopApi['id']);
            if( ! $shop || ! $shop->status ) {
                $this->shopService->firstInstallApps($shopApi);
            }

            $this->shopService->createOrUpdate($shopApi['id'], $shopApi);

            $url  = config('app.url').'/auth?token=' . Socialhead::encodeToken([
                    'id' => $shopApi['id'],
                    'myshopify_domain' => $shopApi['myshopify_domain'],
                    'access_token' => $shopApi['access_token']
                ]);
            event(new AfterAuthAppEvent($shop));
            return redirect()->to($url);
//        } catch (\Exception $exception) {
//            throw new \Exception($exception->getMessage());
//        }
    }

    public function webhooks(Request $request)
    {
        $webhookService = app(WebhookService::class);
        $myshopifyDomain = $request->header('X-Shopify-Shop-Domain');
        $topic = $request->header('X-Shopify-Topic');
        $payload = $request->all();
        switch ($topic) {
            case 'app/uninstalled' :
                $webhookService->uninstallShop($payload);
                break;
            case 'shop/update' :
                $webhookService->shopUpdate($payload);
                break;
            default :
                break;
        }

        event(new WebhooksEvent($myshopifyDomain, $topic, $payload));

        return response()->json(['status' => true]);
    }

    public function getProducts(Request $request) {
        $relNext = $request->input('relNext', null);
        $revPrev = $request->input('relPrev', null);
        $keyword = $request->input('keyword', null);
        $shop = $this->shopService->get($request->input('shop')['id']);

        if (!$shop) {
            return response('shop_not_found', 400);
        }
        $limit = config('common.per_page');

        $products = $this->productQl->setParam($shop['myshopify_domain'], $shop['access_token'])
            ->getProducts($relNext, $revPrev, $keyword, $limit);

        if(isset($products['errors']))
            return response()->json($products['errors'], 500);

        return response()->json($products['data']);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function getProductDetail($id, Request $request)
    {
        $shop = $this->shopService->get($request->input('shop')['id']);

        if (!$shop)
            return response('shop_not_found', 400);

        $product = $this->productApi->setParameter($shop['myshopify_domain'], $shop['access_token'])->get($id);
        if( ! $product['status'])
            return response()->json($product);

        return response()->json($product['data']);

    }
}
