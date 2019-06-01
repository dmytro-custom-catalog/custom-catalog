<?php

namespace Dmytro\CustomCatalog\Model;

use Dmytro\CustomCatalog\Api\CustomProductRepositoryInterface;
use Dmytro\CustomCatalog\Helper\AllowedAttributes;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class CustomProductRepository
 * @package Dmytro\CustomCatalog\Model
 */
class CustomProductRepository implements CustomProductRepositoryInterface
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
     * @var RequestInterface
     */
    private $_request;

    /**
     * @var AllowedAttributes
     */
    private $allowedAttributes;

    /**
     * @var PublisherInterface
     */
    private $_messagePublisher;

    /**
     * @var Json
     */
    private $_jsonSerializer;

    /**
     * CustomProductRepository constructor.
     * @param ProductRepository $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param AllowedAttributes $allowedAttributes
     * @param PublisherInterface $messagePublisher
     * @param Json $json
     */
    public function __construct(
        ProductRepository $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        AllowedAttributes $allowedAttributes,
        PublisherInterface $messagePublisher,
        Json $json
    ) {
        $this->_productRepository = $productRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_request = $request;
        $this->allowedAttributes = $allowedAttributes;
        $this->_messagePublisher = $messagePublisher;
        $this->_jsonSerializer = $json;
    }

    /**
     * Get info about product list by VPN
     *
     * @param string $vpn
     * @return ProductInterface[]|void
     * @throws LocalizedException
     */
    public function getByVPN(string $vpn)
    {
        $searchCriteria = $this->_searchCriteriaBuilder
            ->addFilter('vpn', $vpn)
            ->create();
        $productItems = $this->_productRepository->getList($searchCriteria)->getItems();
        if (empty($productItems)) {
            throw new LocalizedException(
                __(
                    'No products found by this VPN.'
                )
            );
        }
        return $productItems;
    }

    /**
     * @return ProductInterface|mixed|null
     * @throws LocalizedException
     */
    public function update()
    {
        $productId = $this->_request->getParam('entity_id');
        if (!$productId) {
            throw new LocalizedException(
                __('You should specify product entity_id.')
            );
        }
        $storeId = (int) $this->_request->getParam('store', 0);
        $attributeValues = [];
        foreach ($this->allowedAttributes->getList() as $attributeCode) {
            $attributeValue = $this->_request->getParam($attributeCode, false);

            if (false !== $attributeValue) {
                $attributeValues[$attributeCode] = $attributeValue;
            }
        }
        if (empty($attributeValues)) {
            throw new LocalizedException(
                __(
                    'You did not specified any attribute from allowed ones: ' .
                    implode($this->allowedAttributes->getList(), ', ') . '.'
                )
            );
        }

        return $this->_messagePublisher->publish(
            'dmytro.product.update',
            $this->_jsonSerializer->serialize([
                'product_id' => $productId,
                'store_id' => $storeId,
                'attributes' => $attributeValues
            ])
        );
    }
}
