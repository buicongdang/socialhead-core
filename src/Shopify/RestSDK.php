<?php
namespace Socialhead\Core\Shopify;

use GuzzleHttp\Client;

class RestSDK
{
    private $shopify_version, $myshopify_domain, $access_token;
    private $path, $path_child, $method, $child, $object_id;

    private $client;
    private $resource = [
        'Products' => [
            'path' => 'products',
            'method' => [
                'lists', 'get', 'count', 'delete', 'post', 'put', 'delete'
            ],
            'child' => [
                'Images' => [
                    'path' => 'images',
                    'method' => [
                        'lists', 'get', 'count', 'delete', 'post', 'put', 'delete'
                    ]
                ],
                'Variants' => [
                    'path' => 'variants',
                    'method' => [
                        'lists', 'get', 'count', 'delete', 'post', 'put', 'delete'
                    ]
                ]
            ]
        ],
        'Themes' => [
            'path' => 'themes',
            'method' => [
                'lists', 'get', 'count', 'delete', 'post', 'put', 'delete'
            ],
            'child' => [
                'Assets' => [
                    'path' => 'assets',
                    'method' => [
                        'lists', 'get', 'count', 'delete', 'post', 'put'
                    ]
                ]
            ]
        ],
        'Pages' => [
            'path' => 'pages',
            'method' => [
                'lists', 'get', 'count', 'delete', 'post', 'put'
            ]
        ],
        'RecurringApplicationCharge' => [
            'path' => 'recurring_application_charges',
            'method' => [
                'lists', 'get', 'delete', 'post', 'put'
            ]
        ],
        'ScriptTag' => [
            'path' => 'script_tags',
            'method' => [
                'lists', 'get', 'count', 'delete', 'post', 'put'
            ]
        ],
        'Shops' => [
            'path' => 'shop',
            'method' => [
                'lists'
            ]
        ],
        'Webhooks' => [
            'path' => 'webhooks',
            'method' => [
                'lists', 'post', 'delete'
            ]
        ]
    ];

    /**
     * @param $name
     * @param null $arguments
     * @return $this
     * @throws \Exception
     */
    public function __call($name, $arguments = null)
    {
        if( ! in_array($name, array_keys($this->resource)))
            throw new \Exception("Resource not support");

        $resource = $this->resource[$name];

        $this->path = $resource['path'];
        $this->method = $resource['method'];
        $this->child = isset($resource['child']) ? $resource['child'] : [];

        if($arguments)
            $this->object_id = $arguments[0];

        return $this;
    }

    /**
     * @param $name
     * @return $this
     * @throws \Exception
     */
    public function __get($name)
    {
        if( ! in_array($name, array_keys($this->child)))
            throw new \Exception('Resource child not support');

        $this->method = $this->child[$name]['method'];

        $this->path_child = $this->child[$name]['path'];
        return $this;
    }

    public function __construct($arg)
    {
        $this->shopify_version = config('socialhead.services.shopify.api_version');
        $this->myshopify_domain = $arg['myshopify_domain'];
        $this->access_token = $arg['access_token'];
        $this->client = new Client();
    }

    /**
     * @param $arg
     * @return RestSDK
     */
    static function config($arg)
    {
        return new RestSDK($arg);
    }

    /**
     * @param null $id
     * @param null $count
     * @return string
     */
    private function buildUrl($id = null, $count = null)
    {
        $url = "https://$this->myshopify_domain/admin/api/$this->shopify_version/$this->path";

        if( ! empty($this->object_id))
            $url .= "/$this->object_id";

        if( ! empty($this->path_child))
            $url .= "/$this->path_child";

        if(isset($id))
            $url .= "/$id";

        if(isset($count))
            $url .= '/count';

        $url .= '.json';
        return $url;
    }

    /**
     * @param null $id
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get($id = null)
    {
        if( ! in_array('get', $this->method))
            throw new \Exception('Method not support in resource');

        $url = $this->buildUrl($id);

        try {
            $result =  $this->client->request('GET', $url, [
                'headers' => [
                    'Content-Type'           => 'application/json',
                    'X-Shopify-Access-Token' => $this->access_token
                ],
            ]);
            return ['status' => true, 'data' => json_decode($result->getBody()->getContents(), true)];
        } catch (\Exception $exception) {
            return ['status' => false, 'message' => $exception->getMessage()];
        }

    }

    /**
     * @param array $filters
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function lists($filters = [])
    {
        if( ! in_array('lists', $this->method))
            throw new \Exception('Method not support in resource');

        $url = $this->buildUrl();

        try {
            $result = $this->client->request('GET', $url, [
                'headers' => [
                    'Content-Type'           => 'application/json',
                    'X-Shopify-Access-Token' => $this->access_token
                ],
                'query' => $filters
            ]);
            return ['status' => true, 'data' => json_decode($result->getBody()->getContents(), true)];
        } catch (\Exception $exception) {
            return ['status' => false, 'message' => $exception->getMessage()];
        }
    }

    /**
     * @param array $filters
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function count($filters = [])
    {
        if( ! in_array('count', $this->method))
            throw new \Exception('Method not support in resource');

        $url = $this->buildUrl(null, true);
        try {
            $result = $this->client->request('GET', $url, [
                'headers' => [
                    'Content-Type'           => 'application/json',
                    'X-Shopify-Access-Token' => $this->access_token
                ],
                'query' => $filters
            ]);
            return ['status' => true, 'data' => json_decode($result->getBody()->getContents(), true)];
        } catch (\Exception $exception) {
            return ['status' => false, 'message' => $exception->getMessage()];
        }
    }

    /**
     * @param $id
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete($id)
    {
        if( ! in_array('delete', $this->method))
            throw new \Exception('Method not support in resource');

        $url = $this->buildUrl($id);

        try {
            $result = $this->client->request('DELETE', $url, [
                'headers' => [
                    'Content-Type'           => 'application/json',
                    'X-Shopify-Access-Token' => $this->access_token
                ]
            ]);
            return ['status' => true, 'data' => json_decode($result->getBody()->getContents(), true)];
        } catch (\Exception $exception) {
            return ['status' => false, 'message' => $exception->getMessage()];
        }
    }

    /**
     * @param $arg
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post($arg)
    {
        if( ! in_array('post', $this->method))
            throw new \Exception('Method not support in resource');

        $url = $this->buildUrl();

        try {
            $result = $this->client->request('POST', $url, [
                'headers' => [
                    'Content-Type'           => 'application/json',
                    'X-Shopify-Access-Token' => $this->access_token
                ],
                'body' => json_encode($arg)
            ]);
            return ['status' => true, 'data' => json_decode($result->getBody()->getContents(), true)];
        } catch (\Exception $exception) {
            return ['status' => false, 'message' => $exception->getMessage()];
        }
    }

    /**
     * @param $id
     * @param $arg
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function put($id, $arg)
    {
        if( ! in_array('post', $this->method))
            throw new \Exception('Method not support in resource');

        $url = $this->buildUrl($id);

        try {
            $result = $this->client->request('PUT', $url, [
                'headers' => [
                    'Content-Type'           => 'application/json',
                    'X-Shopify-Access-Token' => $this->access_token
                ],
                'body' => $arg
            ]);
            return ['status' => true, 'data' => json_decode($result->getBody()->getContents(), true)];
        } catch (\Exception $exception) {
            return ['status' => false, 'message' => $exception->getMessage()];
        }
    }
}
