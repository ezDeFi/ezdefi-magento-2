<?php

namespace Ezdefi\Payment\Ui\Component\ExceptionConfirmed\Column;

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
                // cut '0' in last of amount
                $lengthToCut = 0;
                for($i = strlen($items['amount_id']) - 1; $i > 0; $i--) {
                    if($items['amount_id'][$i] === '0') {
                        $lengthToCut++;
                    } else {
                        break;
                    }
                }
                $amount = substr($items['amount_id'], 0, strlen($items['amount_id']) - $lengthToCut);

                if($amount[strlen($amount) - 1] === '.') {
                    $amount = substr($amount,0, -1);
                }

                $amountHtml = '<p>'.$amount.'</p>';
                if (!$items['order_id']) {
                    $amountHtml .= '<a href="'.$items['explorer_url'].'" target="_blank">View Transaction Detail</a>';
                }

                $items['amount_id'] = $amountHtml;

            }
        }
        return $dataSource;
    }
}