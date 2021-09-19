<?php
namespace Socialhead\Core\Services;

use Socialhead\Core\Repository\ShopRepository;

class ShopService
{
    private $shopRepository;
    public function __construct(ShopRepository $shopRepository)
    {
        $this->shopRepository = $shopRepository;
    }

    function firstInstallApps($shop)
    {

    }

    /**
     * @param $shopId
     * @return mixed
     */
    function get($shopId)
    {
        return $this->shopRepository->get($shopId);
    }

    /**
     * @param $id
     * @param $payload
     * @return mixed
     */
    function createOrUpdate($id, $payload)
    {
        return $this->shopRepository->createOrUpdate($id, $payload);
    }
}
