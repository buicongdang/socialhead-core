<?php


namespace Socialhead\Core\Shopify;


use GuzzleHttp\Client;

class AuthSDK
{
    public function __construct()
    {
    }

    /**
     * @param $domain
     * @return string
     */
    public static function generateUrl($domain)
    {
        $shop = $domain;

        $shopifyConfig = config('socialhead.services.shopify');

        $scopes = implode(',', $shopifyConfig['scopes']);
        return "https://{$shop}.myshopify.com/admin/oauth/authorize?client_id={$shopifyConfig['api_key']}&scope={$scopes}&redirect_uri={$shopifyConfig['callback_url']}";
    }

    /**
     * @param $data
     * @return bool
     */
    public static function verifyRequest($data)
    {
        $tmp = [];
        if (is_string($data)) {
            $each = explode('&',$data);
            foreach($each as $e) {
                [$key, $val] = explode('=', $e);
                $tmp[$key] = $val;
            }
        } elseif(is_array($data)) {
            $tmp = $data;
        } else {
            return false;
        }

        // Timestamp check; 1 hour tolerance
        if(($tmp['timestamp'] - time() > 3600 ) ) {
            return false;
        }

        if(array_key_exists('hmac', $tmp)) {
            // HMAC Validation
            $queries = $tmp;
            unset($queries['hmac']);
            ksort($queries);

            $queryString = http_build_query($queries);
            $match       = $tmp['hmac'];
            $calculated  = hash_hmac('sha256', $queryString, config('socialhead.services.shopify.api_secret'));

            return $calculated === $match;
        }

        return false;
    }

    /**
     * @param $shop
     * @param $code
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getAccessToken($shop, $code)
    {
        $client = new Client();
        $response = $client->request('POST', "https://{$shop}/admin/oauth/access_token.json",
            [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode([
                    'code' => $code,
                    'client_id' => config('socialhead.services.shopify.api_key'),
                    'client_secret' => config('socialhead.services.shopify.api_secret')
                ])
            ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
