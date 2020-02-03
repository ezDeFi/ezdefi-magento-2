<?php
namespace Ezdefi\Payment\Model\Api;

use Ezdefi\Payment\Api\GetCoinsInterface;
use Ezdefi\Payment\Helper\GatewayHelper;
use \Magento\Framework\Webapi\Rest\Request;
use \Magento\Framework\UrlInterface;

class getCoins implements GetCoinsInterface
{

    private $_gatewayHelper;
    protected $_request;

    public function __construct(
        GatewayHelper $gatewayHelper,
        UrlInterface $urlInterface,
        Request $request
    )
    {
        $this->_gatewayHelper = $gatewayHelper;
        $this->_request = $request;
        $this->_urlInterface = $urlInterface;
    }

    public function getCoins() {
        $request = $this->_request->getParams();
        $tokenList = $this->_gatewayHelper->getListToken($request['keyword'], $this->_urlInterface->getBaseUrl());
        return $tokenList;
    }
}