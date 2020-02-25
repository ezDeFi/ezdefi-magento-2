<?php

namespace Ezdefi\Payment\Model;

use Ezdefi\Payment\Helper\GatewayHelper;

class Currency implements \Magento\Framework\Data\OptionSourceInterface
{
    protected $_gatewayHelper;


    public function __construct(
        GatewayHelper $gatewayHelper
    )
    {
        $this->_gatewayHelper = $gatewayHelper;
    }

    public function getOptions()
    {
        $result     = [];
        $currencies = $this->_gatewayHelper->getCurrencies();

        foreach ($currencies as $currency) {
            $result[] = [
                'value' => $currency['token']['symbol'],
                'label' => strtoupper($currency['token']['symbol'])
            ];
        }
        return $result;
    }

    public function toOptionArray()
    {
        return $this->getOptions();
    }

}