<?php

namespace Ezdefi\Payment\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use \Magento\Backend\Block\Template\Context;
use \Magento\Framework\Data\Form\Element\AbstractElement;
use \Ezdefi\Payment\Model\CurrencyFactory;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;

/**
 * Class CryptoCurrencies
 */
class AcceptablePriceVariable extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_scopeConfig;

    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(AbstractElement $element) {
        $variation= $this->_scopeConfig->getValue('payment/ezdefi_payment/variation');

        $html = '<input id="payment_us_ezdefi_payment_variation" 
                    name="groups[ezdefi_payment][fields][variation][value]" 
                    value="'.$variation.'"
                    class="required-entry input-text admin__control-text valid only-float"
                    data-ui-id="text-groups-ezdefi-payment-fields-variation-value" 
                    data-validate="{
                        min: 0, 
                        max: 100
                    }"
                    type="text">';
        return $html;
    }
}
