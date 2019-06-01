<?php

namespace Dmytro\CustomCatalog\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper as InitializationHelper;
use Magento\Catalog\Controller\Adminhtml\Product\Builder;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class UpdateProduct
 * @package Dmytro\CustomCatalog\Model
 */
class UpdateProduct
{

    /**
     * @var ProductRepositoryInterface
     */
    private $_productRepository;

    /**
     * @var Builder
     */
    private $_productBuilder;

    /**
     * @var InitializationHelper
     */
    private $_initializationHelper;

    /**
     * UpdateAttributes constructor.
     * @param ProductRepositoryInterface $productRepository
     * @param Builder $productBuilder
     * @param InitializationHelper $initializationHelper
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        Builder $productBuilder,
        InitializationHelper $initializationHelper
    ) {
        $this->_productRepository = $productRepository;
        $this->_productBuilder = $productBuilder;
        $this->_initializationHelper = $initializationHelper;
    }

    /**
     * @param RequestInterface $request
     * @throws LocalizedException
     */
    public function update(RequestInterface $request)
    {
        $product = $this->_initializationHelper->initialize(
            $this->_productBuilder->build($request)
        );
        $product->save();
    }
}
