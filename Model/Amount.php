<?php
namespace Ezdefi\Payment\Model;

class Amount extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'ezdefi_amount';

    protected $_cacheTag = 'ezdefi_amount';

    protected $_eventPrefix = 'ezdefi_amount';

    protected function _construct()
    {
        $this->_init('Ezdefi\Payment\Model\ResourceModel\Amount');
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