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
class PublicKey extends \Magento\Config\Block\System\Config\Form\Field
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
        $publicKey = $this->_scopeConfig->getValue('payment/ezdefi_payment/public_key');

        $html = '<input id="payment_us_ezdefi_payment_public_key" 
                    name="groups[ezdefi_payment][fields][public_key][value]" 
                    value="'.$publicKey.'"
                    class="required-entry input-text admin__control-text ezdefi__public-key"
                    data-ui-id="text-groups-ezdefi-payment-fields-public-key-value" 
                    data-validate="{
                        required: true,
                        remote: {
                            url: \'/rest/V1/ezdefi/gateway/checkpublickey\',
                            type: \'get\',
                            data: {
                                    gateway_url: function() { return $(\'#payment_us_ezdefi_payment_gateway_api_url\').val() },
                                    api_key: function() { return $(\'#payment_us_ezdefi_payment_api_key\').val() }
                                }
                            },
                        messages: {
                            remote:  \'This Site Id is invalid\'
                        }}"
                    type="text">';
        return $html;
    }
}
