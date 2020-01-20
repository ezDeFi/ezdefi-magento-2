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
    CONST URL_CHECK_API_KEY = 'admin/gateway/checkapikey';

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
        $_scopeConfig = $scopeConfig;
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(AbstractElement $element) {
        $apiKey = $this->_scopeConfig->getValue('payment/ezdefi_payment/api_key');

        $html = '<input id="payment_us_ezdefi_payment_api_key" 
                    name="groups[ezdefi_payment][fields][api_key][value]" 
                    value="'.$apiKey.'"
                    class="required-entry input-text admin__control-text valid"
                    data-ui-id="text-groups-ezdefi-payment-fields-api-key-value" 
                    data-validate="{
                        required: true,
                        remote: {url: \''.$this->_urlBuilder->getUrl(self::URL_CHECK_API_KEY).'\', type: \'get\'},
                        messages: {
                            remote:  \'This Api Key is invalid\'
                        }}"
                    type="text">';
        return $html;
    }
}
