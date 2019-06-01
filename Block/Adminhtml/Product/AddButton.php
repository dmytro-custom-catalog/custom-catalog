<?php

namespace Dmytro\CustomCatalog\Block\Adminhtml\Product;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ProductTypes\Config;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class AddButton
 * @package Dmytro\CustomCatalog\Block\Adminhtml\Product
 */
class AddButton extends Template implements ButtonProviderInterface
{

    /**
     * @var ProductFactory
     */
    protected $_productFactory;

    /**
     * @var Config
     */
    protected $_config;

    /**
     * AddButton constructor.
     * @param ProductFactory $productFactory
     * @param Context $context
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        ProductFactory $productFactory,
        Context $context,
        Config $config,
        array $data = []
    ) {
        $this->_productFactory = $productFactory;
        $this->_config = $config;
        parent::__construct($context, $data);
    }

    /**
     * Save button
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Add New Product'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'add']],
                'form-role' => 'add',
            ],
            'sort_order' => 30,
            'url' => $this->_getProductCreateUrl()
        ];
    }

    /**
     * Retrieve product create url of default product type
     *
     * @return string
     */
    protected function _getProductCreateUrl()
    {
        return $this->getUrl(
            'catalog/product/new',
            [
                'set' => $this->_productFactory->create()->getDefaultAttributeSetId(),
                'type' => ProductType::DEFAULT_TYPE
            ]
        );
    }
}
