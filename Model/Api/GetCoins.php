<?php
namespace Ezdefi\Payment\Model\Api;

use Ezdefi\Payment\Api\GetCoinsInterface;
use Ezdefi\Payment\Helper\GatewayHelper;

class getCoins implements GetCoinsInterface
{

    private $_gatewayHelper;

    public function __construct(GatewayHelper $gatewayHelper)
    {
        $this->_gatewayHelper = $gatewayHelper;
    }

    public function getCoins() {
        $tokenList = $this->_gatewayHelper->getListToken();
        return $tokenList;
    }
}