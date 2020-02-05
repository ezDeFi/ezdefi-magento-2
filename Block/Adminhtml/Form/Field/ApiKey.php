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
class ApiKey extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_currencyFactory;
    protected $_scopeConfig;
    protected $_urlBuilder;

    public function __construct(
        Context $context,
        CurrencyFactory $currencyFactory,
        ScopeConfigInterface $scopeConfig,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        $this->_currencyFactory = $currencyFactory;
        $this->_urlBuilder = $urlBuilder;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(AbstractElement $element) {
        $apiKey = $this->_scopeConfig->getValue('payment/ezdefi_payment/api_key');

        $html = '<input id="payment_us_ezdefi_payment_api_key" 
                    name="groups[ezdefi_payment][fields][api_key][value]" 
                    value="'.$apiKey.'"
                    class="required-entry input-text admin__control-text valid ezdefi__api-key"
                    data-ui-id="text-groups-ezdefi-payment-fields-api-key-value" 
                    data-validate="{
                        required: true,
                        remote: {
                            url: \'/rest/V1/ezdefi/gateway/checkapikey\',
                            type: \'get\',
                            data: {
                                    gateway_url: function() { return $(\'#payment_us_ezdefi_payment_gateway_api_url\').val() }
                                }
                            },
                        messages: {
                            remote:  \'This Api Key is invalid\'
                        }}"
                    type="text"
                    onChange=""
                    >';
        return $html;
    }
}
