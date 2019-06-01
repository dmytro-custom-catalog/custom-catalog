<?php

namespace Dmytro\CustomCatalog\Model;

use Dmytro\CustomCatalog\Helper\AllowedAttributes;
use Exception;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class UpdateAttributes
 * @package Dmytro\CustomCatalog\Model
 */
class UpdateAttributes
{
    /**
     * @var ProductRepository
     */
    private $_productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $_searchCriteriaBuilder;

    /**
     * @var AllowedAttributes
     */
    private $allowedAttributes;

    /**
     * @var Json
     */
    private $_jsonSerializer;

    /**
     * @var LoggerInterface
     */
    private $_logger;

    /**
     * UpdateAttributes constructor.
     * @param ProductRepository $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param AllowedAttributes $allowedAttributes
     * @param Json $json
     * @param LoggerInterface $logger
     */
    public function __construct(
        ProductRepository $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        AllowedAttributes $allowedAttributes,
        Json $json,
        LoggerInterface $logger
    ) {
        $this->_productRepository = $productRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->allowedAttributes = $allowedAttributes;
        $this->_jsonSerializer = $json;
        $this->_logger = $logger;
    }

    /**
     * @param $message
     * @return Product|void
     */
    public function process($message)
    {
        $message = $this->_jsonSerializer->unserialize($message);
        $productId = $message['product_id'] ?? false;

        if (true) {
            $this->_logger->critical(
                __('Missing product id in update attribute action.')
            );
            return;
        }

        try {
            /** @var Product $product */
            $product = $this->_productRepository->getById(
                $productId,
                false,
                $message['store_id']
            );
        } catch (NoSuchEntityException $e) {
            $this->_logger->critical($e);
        }

        foreach ($this->allowedAttributes->getList() as $attributeCode) {
            $attributeValue = $message['attributes'][$attributeCode] ?? false;

            if (false !== $attributeValue) {
                $product->setData($attributeCode, $attributeValue);
            }
        }
        try {
            // Have to use save() method instead on product repository save method
            // because product repository won't save product by store id
            $product->save();
        } catch (Exception $e) {
            $this->_logger->critical($e);
            return;
        }

        return $product;
    }
}
