<?php

namespace Ezdefi\PaymentMethod\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

/**
 * Class SampleConfigProvider
 */
class SampleConfigProvider implements ConfigProviderInterface
{

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'foo' => [
                'bar' => 'data',
            ],
        ];
    }
}