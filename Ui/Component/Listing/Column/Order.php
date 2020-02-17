<?php

namespace Ezdefi\Payment\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class Order extends Column
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
                if($items['order_id']) {
                    $payStatus= 'Not paid';
                    if($items['paid'] == 1) {
                        $payStatus = 'Paid on time';
                    } else if ($items['paid'] == 2) {
                        $payStatus = 'Paid after expired';
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
                                <td class="border-none">'.($items['has_amount'] ? 'No' : 'Yes').'</td>
                            </tr>
                            '.$explorerUrlRow.'
                        </tbody>
                    </table>';
                }
                $orderHtml .= '<select class="ezdefi__select-pending-order" id="ezdefi__select-pending-order-'.$items['id'].'" style="width: 300px" data-check-loaded="1" data-url-get-order="'.$this->_urlBuilder->getUrl(self::URL_GET_ORDER).'">
                        <option value=""></option>
                    </select>
                    <button class="ezdefi__btn-show-list-order-pending" id="ezdefi__btn-show-list-order-pending-'.$items['id'].'" data-exception-id="'.$items['id'].'">
                        Assign other order
                    </button>
                    <br>
                    <button class="ezdefi__btn-cancel-assign" id="ezdefi__btn-cancel-assign-'.$items['id'].'" data-exception-id="'.$items['id'].'"">
                        Cancel
                    </button>
                    <button class="ezdefi__btn-assign-order" id="ezdefi__btn-assign-order-'.$items['id'].'"
                        data-url-assign="'.
                    $this->_urlBuilder->getUrl(self::URL_ASSIGN_ORDER,                                 [
                        'id' => $items['id']
                    ]).'">Assign</button>';

                $items['order_id'] = $orderHtml;
            }
        }
        return $dataSource;
    }
}