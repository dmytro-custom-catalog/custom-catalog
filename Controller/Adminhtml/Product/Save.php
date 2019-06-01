<?php

namespace Dmytro\CustomCatalog\Controller\Adminhtml\Product;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Catalog\Controller\Adminhtml\Product;
use Magento\Catalog\Controller\Adminhtml\Product\Builder;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Dmytro\CustomCatalog\Model\UpdateProduct;

/**
 * Class Save
 * @package Dmytro\CustomCatalog\Controller\Adminhtml\Product
 */
class Save extends Product implements HttpPostActionInterface
{
    /**
     * @var UpdateProduct
     */
    private $updateProduct;

    /**
     * @var Escaper|null
     */
    private $escaper;

    /**
     * @var null|LoggerInterface
     */
    private $logger;

    /**
     * Save constructor.
     * @param Context $context
     * @param Builder $productBuilder
     * @param UpdateProduct $updateProduct
     * @param Escaper|null $escaper
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        Context $context,
        Builder $productBuilder,
        UpdateProduct $updateProduct,
        Escaper $escaper = null,
        LoggerInterface $logger = null
    ) {
        parent::__construct($context, $productBuilder);
        $this->escaper = $escaper ?? $this->_objectManager->get(Escaper::class);
        $this->logger = $logger ?? $this->_objectManager->get(LoggerInterface::class);
        $this->updateProduct = $updateProduct;
    }

    /**
     * Save product action
     *
     * @return Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $storeId = $this->getRequest()->getParam('store', 0);
        $redirectBack = $this->getRequest()->getParam('back', false);
        $data = $this->getRequest()->getPostValue();
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $productId = $this->getRequest()->getParam('id');
        $productAttributeSetId = $this->getRequest()->getParam('set');

        if ($data) {
            try {
                $this->updateProduct->update($this->getRequest());
                $this->messageManager->addSuccessMessage(__('You saved the product.'));
            } catch (LocalizedException $e) {
                $this->logger->critical($e);
                $this->messageManager->addExceptionMessage($e);
            } catch (Exception $e) {
                $this->logger->critical($e);
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        } else {
            $resultRedirect->setPath('custom_catalog/*/', ['store' => $storeId]);
            $this->messageManager->addErrorMessage('No data to save');
            return $resultRedirect;
        }

        if ($redirectBack) {
            // redirect to page where we came from in case of success
            $resultRedirect->setPath(
                'custom_catalog/*/edit',
                ['id' => $productId, '_current' => true, 'set' => $productAttributeSetId]
            );
        } else {
            $resultRedirect->setPath('custom_catalog/*/', ['store' => $storeId]);
        }
        return $resultRedirect;
    }
}
