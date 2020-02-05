<?php
namespace Ezdefi\Payment\Model\Api;

use Ezdefi\Payment\Api\CheckApiKeyInterface;
use Ezdefi\Payment\Helper\GatewayHelper;
use \Magento\Framework\Webapi\Rest\Request;

class CheckApiKey implements CheckApiKeyInterface
{
    private $_gatewayHelper;
    protected $_request;

    public function __construct(
        GatewayHelper $gatewayHelper,
        Request $request
    )
    {
        $this->_gatewayHelper = $gatewayHelper;
        $this->_request = $request;
    }

    public function checkApiKey() {
        $request = $this->_request->getParams();
        $apikey= $request['groups']['ezdefi_payment']['fields']['api_key']['value'];
        $gatewayUrl = $request['gateway_url'];

        return $this->_gatewayHelper->checkApiKey($apikey, $gatewayUrl);
    }
}