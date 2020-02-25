<?php

namespace Ezdefi\Payment\Model\Api;

use Ezdefi\Payment\Api\CheckPublicKeyInterface;
use Ezdefi\Payment\Helper\GatewayHelper;
use \Magento\Framework\Webapi\Rest\Request;

class CheckPublicKey implements CheckPublicKeyInterface
{
    private   $_gatewayHelper;
    protected $_request;

    public function __construct(
        GatewayHelper $gatewayHelper,
        Request $request
    )
    {
        $this->_gatewayHelper = $gatewayHelper;
        $this->_request       = $request;
    }

    public function checkPublicKey()
    {
        $request    = $this->_request->getParams();
        $publicKey  = $request['groups']['ezdefi_payment']['fields']['public_key']['value'];
        $gatewayUrl = $request['gateway_url'];
        $apikey     = $request['api_key'];

        return $this->_gatewayHelper->checkPublicKey($publicKey, $apikey, $gatewayUrl);
    }
}