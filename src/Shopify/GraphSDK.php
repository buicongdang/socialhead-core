<?php


namespace Socialhead\Core\Shopify;

use GuzzleHttp\Client;

class GraphSDK
{
    private $client, $access_token, $url;
    public function __construct($arg)
    {
        $this->access_token = $arg['access_token'];
        $this->url    = "https://{$arg['myshopify_domain']}/admin/api/".config('socialhead.services.shopify.api_version')."/graphql.json";
        $this->client = new Client();

        return $this;
    }

    /**
     * @param string $query
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function graphqlQuery(string $query)
    {
        try {
            $data = $this->client->request('POST', $this->url, [
                'headers' => [
                    'Accept'                 => 'application/json',
                    'Content-Type'           => 'application/json',
                    'X-Shopify-Access-Token' => $this->access_token
                ],
                'body'    => $query
            ]);

            return ['status' => true, 'data' => json_decode($data->getBody()->getContents(), true)];
        } catch (\Exception $exception) {
            return ['status' => false, 'message' => $exception->getMessage()];
        }
    }
}
