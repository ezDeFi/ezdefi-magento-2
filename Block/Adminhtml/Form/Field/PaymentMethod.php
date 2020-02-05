<?php

namespace Ezdefi\Payment\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use \Magento\Backend\Block\Template\Context;
use \Magento\Framework\Data\Form\Element\AbstractElement;
use \Ezdefi\Payment\Model\CurrencyFactory;
use \Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class CryptoCurrencies
 */
class PaymentMethod extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_currencyFactory;
    protected $_scopeConfig;

    public function __construct(
        Context $context,
        CurrencyFactory $currencyFactory,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->_currencyFactory = $currencyFactory;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(AbstractElement $element) {
        $paymentMethod = $this->_scopeConfig->getValue('payment/ezdefi_payment/payment_method');
        $simpleMethod = strpos($paymentMethod, 'simple') !== false ? 'checked' : false;
        $ezdefiMethod = strpos($paymentMethod, 'ezdefi') !== false ? 'checked' : false;

        $checkPaymentMethodValue = $simpleMethod === false && $ezdefiMethod === false ? '' : '1';

        $html = '<div class="nested">
                    <div class="field choice admin__field admin__field-option">
                        <input id="payment_us_ezdefi_payment_payment_method_simple" 
                            type="checkbox" 
                            class="admin__control-checkbox ezdefi__simple-payment-checkbox"
                            name="groups[ezdefi_payment][fields][payment_method][value][]" 
                            value="simple"
                            '.$simpleMethod.'
                            >
                        <label for="payment_us_ezdefi_payment_payment_method_simple" class="admin__field-label"><span><b>Pay with any crypto wallet</b></span></label>
                        <div><i>This method will adjust payment amount of each order by an acceptable number to help payment gateway identifying the uniqueness of that order</i></div>
                    </div>
                    <div class="field choice admin__field admin__field-option">
                        <input id="payment_us_ezdefi_payment_payment_method_ezdefi" 
                            type="checkbox" 
                            class="admin__control-checkbox ezdefi__ezdefi-payment-checkbox" 
                            name="groups[ezdefi_payment][fields][payment_method][value][]" 
                            value="ezdefi"
                            '.$ezdefiMethod.'> 
                        <label for="payment_us_ezdefi_payment_payment_method_ezdefi" class="admin__field-label"><span><b>Pay with ezDeFi wallet</b></span></label>
                        <div><i>This method is more powerful when amount uniqueness above method reaches allowable limit. Users just need to install ezDeFi wallet then import their private key to pay using qrCode.</i></div>
                    </div>
                    <input type="hidden" data-validate="{required: true, messages: {required: \'Choose at least one payment method\'}}" class="check-payment-method-input" value="'.$checkPaymentMethodValue.'">
                </div>';
        return $html;
    }
}
