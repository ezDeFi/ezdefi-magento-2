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
                        <div class="ezdefi__list-currency-delete"></div>
                        <table class="admin__control-table">
                            <thead>
                            <tr>
                                <th class="ezdefi__table-head--currency">Currency</th>
                                <th>Discount</th>
                                <th>Payment Lifetime</th>
                                <th class="ezdefi__table-head--wallet-address">Wallet Address</th>
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
                    <p class="ezdefi__currency-symbol">
                        <img src="'.$currencyData['logo'].'" alt="">
                        <span>' . $currencyData['symbol'] . '/' . $currencyData['name'] . '</span>
                    </p>
                </td>
                <td><input type="text" 
                    name="groups[ezdefi_payment][fields][currency][value][edit]['.$currencyData['currency_id'].'][discount]" 
                    class="ezdefi__currency-discount-input validate-not-negative-number"
                    data-validate="{max: 100}"
                    value="'.$currencyData['discount'].'"></td>
                <td><input type="text" 
                    name="groups[ezdefi_payment][fields][currency][value][edit]['.$currencyData['currency_id'].'][lifetime]"
                    class="ezdefi__payment-lifetime-input validate-not-negative-number validate-digits"
                    value="'.$currencyData['payment_lifetime'].'"></td>
                <td><input type="text" 
                    class="ezdefi__wallet-address-input required-entry"
                    name="groups[ezdefi_payment][fields][currency][value][edit]['.$currencyData['currency_id'].'][wallet_address]" 
                    value="'.$currencyData['wallet_address'].'"></td>
                <td><input type="text"
                    class="ezdefi_block-confirmation-input validate-not-negative-number validate-digits"
                    name="groups[ezdefi_payment][fields][currency][value][edit]['.$currencyData['currency_id'].'][block_confirmation]" 
                    value="'.$currencyData['block_confirmation'].'"></td>
                <td><input type="text"
                    class="ezdefi__currency-decimal-input validate-not-negative-number validate-digits"
                    data-validate="{min:2}"
                    name="groups[ezdefi_payment][fields][currency][value][edit]['.$currencyData['currency_id'].'][decimal]"
                    value="'.$currencyData['decimal'].'"></td>
                <td class="col-actions" colspan="1">
                    <button class="action-delete btn-delete-curency-config" type="button" data-currency-id="'.$currencyData['currency_id'].'"><span>Delete</span></button>
                </td>
            </tr>';
        }
        return $html;
    }
}
