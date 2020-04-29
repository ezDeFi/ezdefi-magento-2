<?php
namespace Ezdefi\Payment\Block\Frontend;

use \Magento\Framework\View\Element\Template\Context;
use Ezdefi\Payment\Helper\GatewayHelper;

class EzdefiMethod extends \Magento\Framework\View\Element\Template
{
    protected $_data;
    protected $_gatewayHelper;

    public function __construct(
        GatewayHelper $gatewayHelper,
        Context $context,
        $data
    )
    {
        $this->_gatewayHelper    = $gatewayHelper;
        $this->_data = $data;
        parent::__construct($context);
    }

    public function isError() {
        return !$this->_data['payment'];
    }

    public function getPaymentId()
    {
        return __($this->_data['payment']->_id);
    }

    public function getOriginCurrency()
    {
        return __($this->_data['payment']->originCurrency);
    }

    public function getOriginValue()
    {
        return __($this->_data['payment']->originValue);
    }

    public function getCryptoCurrency()
    {
        return __($this->_data['payment']->currency);
    }

    public function getCryptoValue()
    {
        $cyptoValue = $this->_gatewayHelper->convertExponentialToFloat($this->_data['payment']->value * pow(10, - $this->_data['payment']->decimal));
        return __($cyptoValue);
    }

    public function getQrCode() {
        return __($this->_data['payment']->qr);
    }

    public function getExpiration() {
        return __($this->_data['payment']->expiredTime);
    }

    public function getDeepLink()
    {
        return __($this->_data['payment']->deepLink);
    }
}