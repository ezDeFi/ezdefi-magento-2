<?php

namespace Ezdefi\Payment\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class Order extends Column
{
    CONST URL_ASSIGN_ORDER = 'admin/exception/assignorder';
    CONST URL_GET_ORDER    = 'admin/exception/getorderpending';

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
        $html = '<button id="ezdefi_test_click">
            <script>
                require([
                    \'jquery\',
                    \'accordion\'  // the alias for "mage/accordion"
                ], function ($) {
                    $(function () { // to ensure that code evaluates on page load
                        $(document).on("click", "#ezdefi_test_click", function () {
                            
                        });

                    });
                });
            </script>';

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$items) {
                $orderHtml = '';
                if($items['order_id']) {
                    $payStatus= 'No';
                    if($items['paid'] === 1) {
                        $payStatus = 'Paid on time';
                    } else if ($items['paid'] === 2) {
                        $payStatus = 'Paid on expiration';
                    }
                    $explorerUrlRow = isset($items['explorer_url']) ? '<tr>
                                <td class="border-none">Explorer url</td>
                                <td class="border-none">:</td>
                                <td class="border-none"><a target="_blank" href="'.$items['explorer_url'].'">'.substr($items['explorer_url'], 0, 50).'</a></td>
                            </tr>' : '';
                    $orderHtml .= '<table>
                        <tbody>
                            <tr>
                                <td class="border-none">Order id</td>
                                <td class="border-none">:</td>
                                <td class="border-none">'.$items['order_id'].'</td>
                            </tr>
                            <tr>
                                <td class="border-none">Expiration</td>
                                <td class="border-none">:</td>
                                <td class="border-none">'.$items['expiration'].'</td>
                            </tr>
                            <tr>
                                <td class="border-none">Paid</td>
                                <td class="border-none">:</td>
                                <td class="border-none">'.$payStatus.'</td>
                            </tr>
                            <tr>
                                <td class="border-none">Pay by ezdefi wallet</td>
                                <td class="border-none">:</td>
                                <td class="border-none">'.($items['has_amount'] ? 'no' : 'yes').'</td>
                            </tr>
                            '.$explorerUrlRow.'
                        </tbody>
                    </table>';

                    $orderHtml .= '<select class="ezdefi__select-pending-order" style="width: 200px" data-check-loaded="1" data-url-get-order="'.$this->_urlBuilder->getUrl(self::URL_GET_ORDER).'">
                        <option value=""></option>
                    </select>
                    <button class="ezdefi__btn-assign-order" 
                        data-url-assign="'.
                            $this->_urlBuilder->getUrl(self::URL_ASSIGN_ORDER,                                 [
                                'id' => $items['id']
                            ]).'">Assign</button>';
                }

                $items['order_id'] = $orderHtml;
            }
        }
        return $dataSource;
    }
}