<?php

namespace Ezdefi\Payment\Model\Adminhtml\System\Config;

use \Magento\Framework\Exception\ValidatorException;
use \Ezdefi\Payment\Model\CurrencyFactory;

class CryptoCurrencies extends \Magento\Framework\App\Config\Value
{

    protected $_currencyFactory;
    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Config\ValueFactory $configValueFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param string $runModelPath
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        CurrencyFactory $currencyFactory,
        array $data = []
    ) {
        $this->_currencyFactory = $currencyFactory;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    public function beforeSave()
    {
        $currenciesData = $this->getValue();

        foreach($currenciesData as $currencyData) {
            $id                 = $currencyData['id'];
            $symbol             = $currencyData['symbol'];
            $name               = $currencyData['name'];
            $logo               = $currencyData['logo'];
            $lifetime           = $currencyData['lifetime'];
            $walletAddress      = $currencyData['wallet_address'];
            $blockConfirmation  = $currencyData['block_confirmation'];
            $decimal            = $currencyData['decimal'];

            if ($symbol == '') {
                throw new ValidatorException(__('Currency symbol is required.'));
            } else if ($name == '') {
                throw new ValidatorException(__('Currency name is required.'));
            } else if ($id == '') {
                throw new ValidatorException(__('Currency id is required.'));
            } else if ($logo == '') {
                throw new ValidatorException(__('Currency logo is required.'));
            } else if (filter_var($lifetime,FILTER_VALIDATE_INT) === false) {
                throw new ValidatorException(__('Payment life time is not a number.'));
            } else if ($walletAddress == '') {
                throw new ValidatorException(__('Wallet address is required.'));
            } else if(filter_var($blockConfirmation, FILTER_VALIDATE_INT) === false) {
                throw new ValidatorException(__('Block confirmation is not a number.'));
            } else if(filter_var($decimal, FILTER_VALIDATE_INT) === false || $decimal <2 || $decimal > 14) {
                throw new ValidatorException(__('Decimal should be number and more than 2, less than 14.'));
            }
        }

        $this->setValue(intval($this->getValue()));

        $this->saveCurrency($currenciesData);

        parent::beforeSave();
    }

    private function saveCurrency($currenciesData) {
        $model = $this->_currencyFactory->create();

        foreach ($currenciesData as $currencyData) {
            $model->addData([
                'currency_id'        => $currencyData['id'],
                'logo'               => $currencyData['logo'],
                'symbol'             => $currencyData['symbol'],
                'name'               => $currencyData['name'],
                'discount'           => $currencyData['discount'],
                'payment_lifetime'   => $currencyData['lifetime'],
                'wallet_address'     => $currencyData['wallet_address'],
                'block_confirmation' => $currencyData['block_confirmation'],
                'decimal'            => $currencyData['decimal'],
                'description'        => $currencyData['description']
            ]);
            $model->save();
        }

    }

}