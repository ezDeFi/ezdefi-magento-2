<?php
namespace Ezdefi\Payment\Model;

class Currency extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
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
}