<?php

namespace Ezdefi\Payment\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use \Magento\Backend\Block\Template\Context;
use \Magento\Framework\Data\Form\Element\AbstractElement;
use \Ezdefi\Payment\Model\CurrencyFactory;
use Magento\Framework\UrlInterface;

/**
 * Class CryptoCurrencies
 */
class CryptoCurrencies extends \Magento\Config\Block\System\Config\Form\Field
{
    CONST URL_GET_COIN    = 'admin/gateway/listcoin';

    protected $_currencyFactory;
    protected $_urlBuilder;

    public function __construct(
        Context $context,
        CurrencyFactory $currencyFactory,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        $this->_currencyFactory = $currencyFactory;
        $this->_urlBuilder = $urlBuilder;
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
                                <th class="ezdefi__table-head--currency">Select coin</th>
                                <th>Discount</th>
                                <th>Expiration (minutes)</th>
                                <th class="ezdefi__table-head--wallet-address">Wallet Address</th>
                                <th>Block Confirmation</th>
                                <th class="coin-decimal">Decimal</th>
                                <th class="col-actions" colspan="1">Action</th>
                            </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <td colspan="7" class="col-actions-add">
                                        <button title="Add" type="button" id="ezdefi-configuration-add-coin" data-url-get-coin="'.$this->_urlBuilder->getUrl(self::URL_GET_COIN).'">
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
        //<span>' . $currencyData['symbol'] . '/' . $currencyData['name'] . '</span>

        $html = '';
        foreach ($currenciesData as $currencyData) {
            $html .= '<tr>
                <td>
                    <p class="ezdefi__currency-symbol">
                        <img src="'.$currencyData['logo'].'" alt="">
                        <span style="text-transform: uppercase">'.$currencyData['symbol'].'</span>
                    </p>
                </td>
                <td><input type="text" 
                    name="groups[ezdefi_payment][fields][currency][value][edit]['.$currencyData['currency_id'].'][discount]" 
                    class="ezdefi__currency-discount-input validate-not-negative-number only-positive-integer"
                    data-validate="{max: 100}"
                    value="'.$currencyData['discount'].'">
                    <span>%</span>
                </td>
                <td><input type="text" 
                    name="groups[ezdefi_payment][fields][currency][value][edit]['.$currencyData['currency_id'].'][lifetime]"
                    class="ezdefi__payment-lifetime-input validate-not-negative-number validate-digits only-positive-integer"
                    value="'.($currencyData['payment_lifetime']/60).'"></td>
                <td><input type="text" 
                    class="ezdefi__wallet-address-input required-entry"
                    name="groups[ezdefi_payment][fields][currency][value][edit]['.$currencyData['currency_id'].'][wallet_address]" 
                    value="'.$currencyData['wallet_address'].'"></td>
                <td><input type="text"
                    class="ezdefi_block-confirmation-input validate-not-negative-number validate-digits only-positive-integer"
                    name="groups[ezdefi_payment][fields][currency][value][edit]['.$currencyData['currency_id'].'][block_confirmation]" 
                    value="'.$currencyData['block_confirmation'].'"></td>
                <td><input type="text"
                    class="ezdefi__currency-decimal-input validate-not-negative-number validate-digits only-positive-integer"
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
