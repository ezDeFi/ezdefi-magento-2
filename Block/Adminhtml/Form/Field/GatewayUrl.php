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
class GatewayUrl extends \Magento\Config\Block\System\Config\Form\Field
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
        $gatewayUrl = $this->_scopeConfig->getValue('payment/ezdefi_payment/gateway_api_url');

        $html = '<input 
                    id="payment_us_ezdefi_payment_gateway_api_url" 
                    name="groups[ezdefi_payment][fields][gateway_api_url][value]" 
                    data-ui-id="text-groups-ezdefi-payment-fields-gateway-api-url-value" 
                    value="'.$gatewayUrl.'" 
                    class="ezdefi__gateway-url validate-url required-entry input-text admin__control-text" 
                    placeholder="http://merchant-api.ezdefi.com/api"
                    type="text">';
        return $html;
    }
}
