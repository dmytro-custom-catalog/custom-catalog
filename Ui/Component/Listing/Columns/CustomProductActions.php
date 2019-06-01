<?php

namespace Dmytro\CustomCatalog\Ui\Component\Listing\Columns;

use Magento\Catalog\Ui\Component\Listing\Columns\ProductActions;

/**
 * Class CustomProductActions
 * @package Dmytro\CustomCatalog\Ui\Component\Listing\Columns
 */
class CustomProductActions extends ProductActions
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $storeId = $this->context->getFilterParam('store_id');

            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['edit'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'custom_catalog/product/edit',
                        ['id' => $item['entity_id'], 'store' => $storeId]
                    ),
                    'label' => __('Edit'),
                    'hidden' => false,
                ];
            }
        }

        return $dataSource;
    }
}
