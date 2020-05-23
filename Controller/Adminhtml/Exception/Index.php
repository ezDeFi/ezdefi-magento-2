<?php

namespace Ezdefi\Payment\Controller\Adminhtml\Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory = false;
    protected $scopeConfig;
    protected $_resourceConfig;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->scopeConfig = $scopeConfig;
        $this->_resourceConfig = $resourceConfig;

    }

    public function execute()
    {
        $lastTimeDelete = $this->scopeConfig->getValue( 'ezdefi/cron/last_time_delete', \Magento\Store\Model\ScopeInterface::SCOPE_STORE );

        if(time() - (int)$lastTimeDelete > 86400 * 7) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $tableName = $resource->getTableName('ezdefi_exception');

            $sql = "DELETE FROM ".$tableName." WHERE DATEDIFF( NOW( ) ,  expiration ) >= 5";
            $connection->query($sql);

            $this->_resourceConfig->saveConfig('ezdefi/cron/last_time_delete',time(),'default', 0);
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((__('ezDeFi Exception Pending')));

        return $resultPage;
    }

}