<?php

namespace Ezdefi\Payment\Ui\Component\ExceptionConfirmed\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class OrderAssign extends Column
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
                $orderHtml = '<table>
                    <tbody>
                        <tr>
                            <td class="border-none" style="width: 130px">Order id</td>
                            <td class="border-none">'.$items['new_increment_id'].'</td>
                        </tr>
                        <tr>
                            <td class="border-none">Email</td>
                            <td class="border-none">'.$items['new_email'].'</td>
                        </tr>
                        <tr>
                            <td class="border-none">Customer</td>
                            <td class="border-none">'.$items['new_customer'].'</td>
                        </tr>
                        <tr>
                            <td class="border-none">Total</td>
                            <td class="border-none">'.$items['new_total'].'</td>
                        </tr>
                        <tr>
                            <td class="border-none">Created at</td>
                            <td class="border-none">'.$items['new_date'].'</td>
                        </tr>
                    </tbody>
                </table>';

                $items['order_assigned'] = $orderHtml;
            }
        }
        return $dataSource;
    }
}