<?php

namespace Ezdefi\Payment\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use \Magento\Backend\Block\Template\Context;
use \Magento\Framework\Data\Form\Element\AbstractElement;
use \Ezdefi\Payment\Model\CurrencyFactory;

/**
 * Class CryptoCurrencies
 */
class CryptoCurrencies extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_currencyFactory;

    public function __construct(
        Context $context,
        CurrencyFactory $currencyFactory,
        array $data = []
    ) {
        $this->_currencyFactory = $currencyFactory;
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(AbstractElement $element) {
        $currenciesElement = $this->oldCurrencyConfig();

        $html = '<div class="design_theme_ua_regexp">
                    <div class="admin__control-table-wrapper">
                        <table class="admin__control-table">
                            <thead>
                            <tr>
                                <th>Currency</th>
                                <th>Discount</th>
                                <th>Payment Lifetime</th>
                                <th>Wallet Address</th>
                                <th>Safe Block Distant</th>
                                <th class="coin-decimal">Decimal</th>
                                <th class="col-actions" colspan="1">Action</th>
                            </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <td colspan="7" class="col-actions-add">
                                        <button title="Add" type="button" id="ezdefi-configuration-add-coin">
                                            <span>Add Coin</span>
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                            <tbody id="ezdefi-configuration-coin-table">
                            '.$currenciesElement.'
                            </tbody>
                        </table>
                    </div>
                </div>';
        return $html;
    }

    private function oldCurrencyConfig() {
        $currenciesData = $this->_currencyFactory->create()->getCollection()->getData();

        $html = '';
        foreach ($currenciesData as $currencyData) {
            $html .= '<tr>
                <td>
                    <img src="'.$currencyData['logo'].'" alt="">
                    <span>'.$currencyData['symbol'].'</span>
                    <span>'.$currencyData['name'].'</span>
                </td>
                <td><input type="text" value="'.$currencyData['discount'].'"></td>
                <td><input type="text" value="'.$currencyData['payment_lifetime'].'"></td>
                <td><input type="text" value="'.$currencyData['wallet_address'].'"></td>
                <td><input type="text" value="'.$currencyData['block_confirmation'].'"></td>
                <td><input type="text" value="'.$currencyData['decimal'].'"></td>
                <td class="col-actions" colspan="1">
                    <button class="action-delete delete-curency-config" type="button"><span>Delete</span></button>
                </td>
            </tr>';
        }
        return $html;
    }
}
