<?php

namespace Ezdefi\Payment\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Order extends Column
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
                $orderHtml = '<table>
                    <tbody>
                        <tr>
                            <td class="border-none">Order id</td>
                            <td class="border-none">'.$items['order_id'].'</td>
                        </tr>
                        <tr>
                            <td class="border-none">Expiration</td>
                            <td class="border-none">'.$items['expiration'].'</td>
                        </tr>
                        <tr>
                            <td class="border-none">Paid</td>
                            <td class="border-none">'.$items['paid'].'</td>
                        </tr>
                        <tr>
                            <td class="border-none">Pay by ezdefi wallet</td>
                            <td class="border-none">'.($items['has_amount'] ? 'no' : 'yes').'</td>
                        </tr>
                        <tr>
                            <td class="border-none">Explorer url</td>
                            <td class="border-none">'.$items['explorer_url'].'</td>
                        </tr>
                    </tbody>
                </table>';

                $items['order_id'] = $orderHtml;
            }
        }
        return $dataSource;
    }
}