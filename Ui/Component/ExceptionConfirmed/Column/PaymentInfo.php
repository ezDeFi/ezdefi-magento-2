<?php

namespace Ezdefi\Payment\Ui\Component\ExceptionConfirmed\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class PaymentInfo extends Column
{
    CONST URL_ASSIGN_ORDER = 'adminhtml/exception/assignorder';
    CONST URL_GET_ORDER    = 'adminhtml/exception/getorderpending';

    protected $_urlBuilder;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$items) {
                $orderHtml = '';
                $explorerUrlRow = isset($items['explorer_url']) ? '<tr>
                                <td class="border-none">Explorer url</td>
                                <td class="border-none">:</td>
                                <td class="border-none"><a target="_blank" href="'.$items['explorer_url'].'">View Transaction Detail</a></td>
                            </tr>' : '';
                if($items['order_id']) {
                    if ($items['paid'] == 1) {
                        $payStatus = 'Paid on time';
                    } else if ($items['paid'] == 2) {
                        $payStatus = 'Paid after expired';
                    } else {
                        $payStatus = 'Not paid';
                    }
                    $explorerUrlRow = isset($items['explorer_url']) ? '<tr>
                                <td class="border-none">Explorer url</td>
                                <td class="border-none">:</td>
                                <td class="border-none"><a target="_blank" href="' . $items['explorer_url'] . '">View Transaction Detail</a></td>
                            </tr>' : '';
                    $orderHtml      .= '<table>
                        <tbody>
                            <tr>
                                <td class="border-none">Expiration</td>
                                <td class="border-none">:</td>
                                <td class="border-none">' . $items['expiration'] . '</td>
                            </tr>
                            <tr>
                                <td class="border-none">Paid</td>
                                <td class="border-none">:</td>
                                <td class="border-none">' . $payStatus . '</td>
                            </tr>
                            <tr>
                                <td class="border-none">Pay by ezdefi wallet</td>
                                <td class="border-none">:</td>
                                <td class="border-none">' . ($items['has_amount'] ? 'No' : 'Yes') . '</td>
                            </tr>
                            ' . $explorerUrlRow . '
                        </tbody>
                    </table>';
                } else {
                    $orderHtml .= '<table>
                        <tbody>
                            '.$explorerUrlRow.'
                        </tbody>
                    </table>';
                }

                $items['payment_id'] = $orderHtml;
            }
        }
        return $dataSource;
    }
}