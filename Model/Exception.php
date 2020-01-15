<?php
namespace Ezdefi\Payment\Model;

class Exception extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'ezdefi_currency';

    protected $_cacheTag = 'ezdefi_exception';

    protected $_eventPrefix = 'ezdefi_exception';

    protected function _construct()
    {
        $this->_init('Ezdefi\Payment\Model\ResourceModel\Exception');
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