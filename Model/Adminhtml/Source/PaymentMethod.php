<?php

namespace Ezdefi\Payment\Model\Adminhtml\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Country
 */
class PaymentMethod implements ArrayInterface
{
    /**
     * Country Helper
     *
     * @var CountryHelper
     */
    private $method;

    /**
     * @param bool $isMultiselect
     * @return array
     */
    public function toOptionArray($isMultiselect = false)
    {
        return [
            ['value' => 'simple', 'label' => 'simple'],
            ['value' => 'ezdefi', 'label' =>'ezdefi']
        ];
    }
}
