<?php

namespace Dmytro\CustomCatalog\Api;

/**
 * Interface CustomProductRepositoryInterface
 * @package Dmytro\CustomCatalog\Api
 */
interface CustomProductRepositoryInterface
{
    /**
     * @return \Magento\Catalog\Api\Data\ProductInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function update();

    /**
     * Get info about product list by VPN
     *
     * @param string $vpn
     * @return \Magento\Catalog\Api\Data\ProductInterface[]|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByVPN(string $vpn);
}
