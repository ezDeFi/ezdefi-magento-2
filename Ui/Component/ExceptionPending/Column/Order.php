<?php

namespace Ezdefi\Payment\Ui\Component\ExceptionPending\Column;

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
                    if($items['paid'] == 1) {
                        $payStatus = 'Paid on time';
                    } else if ($items['paid'] == 2) {
                        $payStatus = 'Paid after expired';
                    } else {
                        $payStatus= 'Not paid';
                    }
                    $orderHtml .= '<table>
                        <tbody>
                            <tr>
                                <td class="border-none" style="width: 130px">Order id</td>
                                <td class="border-none">'.$items['increment_id'].'</td>
                            </tr>
                            <tr>
                                <td class="border-none">Email</td>
                                <td class="border-none">'.$items['email'].'</td>
                            </tr>
                            <tr>
                                <td class="border-none">Customer</td>
                                <td class="border-none">'.$items['customer'].'</td>
                            </tr>
                            <tr>
                                <td class="border-none">Total</td>
                                <td class="border-none">'.$items['total'].'</td>
                            </tr>
                            <tr>
                                <td class="border-none">Created at</td>
                                <td class="border-none">'.$items['date'].'</td>
                            </tr>
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

                $items['increment_id'] = $orderHtml;
            }
        }
        return $dataSource;
    }
}