<?php

namespace Ezdefi\Payment\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Amount extends Column
{
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$items) {
                $amountHtml = '<p>'.(float)$items['amount_id'].'</p>';
                if (!$items['order_id']) {
                    $amountHtml .= '<a href="" target="_blank">'.substr($items['explorer_url'],0,50).'</a>';
                }

                $items['amount_id'] = $amountHtml;

            }
        }
        return $dataSource;
    }
}