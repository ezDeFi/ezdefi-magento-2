<?php
namespace Ezdefi\Payment\Block\Frontend;

use \Magento\Framework\View\Element\Template\Context;

class EzdefiMethod extends \Magento\Framework\View\Element\Template
{
    protected $_data;

    public function __construct(
        Context $context,
        $data
    )
    {
        $this->_data = $data;
        parent::__construct($context);
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
        return __($this->_data['payment']->value);
    }

    public function getQrCode() {
        return __($this->_data['payment']->qr);
    }

    public function getExpiration() {
        return __($this->_data['payment']->expiredTime);
    }
}