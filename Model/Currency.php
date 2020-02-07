<?php
namespace Ezdefi\Payment\Model;

class Currency extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface, \Magento\Framework\Data\OptionSourceInterface
{
    const CACHE_TAG = 'ezdefi_currency';

    protected $_cacheTag = 'ezdefi_currency';

    protected $_eventPrefix = 'ezdefi_currency';

    protected function _construct()
    {
        $this->_init('Ezdefi\Payment\Model\ResourceModel\Currency');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }


    public function getOptions()
    {
        $currencies = $this->getCollection()->setOrder('`order`', 'ASC');
        $res =[];
        foreach ($currencies as $currency) {
            $res[] = ['value' => $currency['symbol'], 'label' => strtoupper($currency['symbol'])];
        }
        return $res;
    }

    public function toOptionArray()
    {
        return $this->getOptions();
    }

}