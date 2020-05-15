<?php

namespace Ezdefi\Payment\Model\Api;

use Ezdefi\Payment\Api\CheckPublicKeyInterface;
use Ezdefi\Payment\Helper\GatewayHelper;
use \Magento\Framework\Webapi\Rest\Request;
use \Magento\Framework\UrlInterface;

class CheckPublicKey implements CheckPublicKeyInterface
{
    private   $_gatewayHelper;
    protected $_request;
    protected $_urlInterface;

    public function __construct(
        GatewayHelper $gatewayHelper,
        Request $request,
        UrlInterface $urlInterface
    )
    {
        $this->_gatewayHelper = $gatewayHelper;
        $this->_request       = $request;
        $this->_urlInterface     = $urlInterface;
    }

    public function checkPublicKey()
    {
        $request    = $this->_request->getParams();
        $publicKey  = $request['groups']['ezdefi_payment']['fields']['public_key']['value'];
        $gatewayUrl = $request['gateway_url'];
        $apikey     = $request['api_key'];

        $status = $this->_gatewayHelper->checkPublicKey($publicKey, $apikey, $gatewayUrl);
        if($status == 'true') {
            $this->_gatewayHelper->updateCallbackUrl($this->_urlInterface->getUrl('ezdefi/frontend/callbackconfirmorder'), $publicKey, $apikey, $gatewayUrl);
        }

        return $status;
    }
}