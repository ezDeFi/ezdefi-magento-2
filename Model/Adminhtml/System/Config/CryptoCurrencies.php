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
        $request = $this->getValue();

        if (isset($request['add'])) {
            $this->validateAddCurrency($request['add'], 'add');
            $this->saveCurrency($request['add']);
        }

        if (isset($request['edit'])) {
            $this->validateAddCurrency($request['edit'], 'edit');
            $this->updateCurrency($request['edit']);
        }

        if (isset($request['ids_delete'])) {
            $this->deleteCurrency($request['ids_delete']);
        }

        $this->setValue(intval($this->getValue()));
        parent::beforeSave();
    }

    private function deleteCurrency($ids) {
        foreach ($ids as $id) {
            $model = $this->_currencyFactory->create();
            $model->getCollection()->addFieldToFilter('currency_id', $id)->walk('delete');
        }
    }

    private function validateAddCurrency($currenciesData, $type) {
        foreach($currenciesData as $currencyData) {
            $id                 = isset($currencyData['id']) ? $currencyData['id'] : '';
            $symbol             = isset($currencyData['symbol']) ? $currencyData['symbol'] : '';
            $name               = isset($currencyData['name']) ? $currencyData['name'] : '';
            $logo               = isset($currencyData['logo']) ? $currencyData['logo'] : '';
            $discount           = isset($currencyData['discount']) ? $currencyData['discount'] : '';
            $lifetime           = isset($currencyData['lifetime']) ? $currencyData['lifetime'] : '';
            $walletAddress      = isset($currencyData['wallet_address']) ? $currencyData['wallet_address'] : '';
            $blockConfirmation  = isset($currencyData['block_confirmation']) ? $currencyData['block_confirmation'] : '';
            $decimal            = isset($currencyData['decimal']) ? $currencyData['decimal'] : '';

            if($type === 'add') {
                if ($symbol == '') {
                    throw new ValidatorException(__('Currency symbol is required.'));
                } else if ($name == '') {
                    throw new ValidatorException(__('Currency name is required.'));
                } else if ($id == '') {
                    throw new ValidatorException(__('Currency id is required.'));
                } else if ($logo == '') {
                    throw new ValidatorException(__('Currency logo is required.'));
                }
            }
            if(filter_var($discount,FILTER_VALIDATE_FLOAT) === false || (float)$discount > 100 || (float)$discount < 0){
                throw new ValidatorException(__('Discount should be float and less than 100.'));
            } else if (filter_var($lifetime,FILTER_VALIDATE_INT) === false || (int)$lifetime < 0) {
                throw new ValidatorException(__('Payment life time is not a positive number.'));
            } else if ($walletAddress == '') {
                throw new ValidatorException(__('Wallet address is required.'));
            } else if(filter_var($blockConfirmation, FILTER_VALIDATE_INT) === false || (int)$blockConfirmation < 0) {
                throw new ValidatorException(__('Block confirmation is not a positive number.'));
            } else if(filter_var($decimal, FILTER_VALIDATE_INT) === false || $decimal <2 || $decimal > 14) {
                throw new ValidatorException(__('Decimal should be number and more than 2, less than 14.'));
            }
        }
    }

    private function saveCurrency($currenciesData) {
        foreach ($currenciesData as $currencyData) {
            $model = $this->_currencyFactory->create();
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

    private function updateCurrency($currenciesData) {
        foreach ($currenciesData as $currencyId => $currencyData) {
            $collection = $this->_currencyFactory->create()->getCollection()->addFieldToFilter('currency_id', $currencyId);
            $currency = $collection->getFirstItem();

            $currency->setData('discount', $currencyData['discount']);
            $currency->setData('payment_lifetime', $currencyData['lifetime']);
            $currency->setData('wallet_address', $currencyData['wallet_address']);
            $currency->setData('block_confirmation', $currencyData['block_confirmation']);
            $currency->setData('decimal', $currencyData['decimal']);

            $currency->save();
        }
    }

}