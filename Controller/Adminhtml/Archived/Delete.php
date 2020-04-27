<?php
namespace Ezdefi\Payment\Controller\Adminhtml\Archived;

use \Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use \Ezdefi\Payment\Model\ExceptionFactory;
use Magento\Framework\UrlInterface;

class Delete extends \Magento\Backend\App\Action
{
    protected $_pageFactory;

    protected $_exceptionFactory;
    protected $_urlBuilder;


    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        PageFactory $pageFactory,
        ExceptionFactory $exceptionFactory,
        UrlInterface $urlBuilder
    )
    {
        $this->_pageFactory = $pageFactory;
        $this->_exceptionFactory = $exceptionFactory;
        $this->_urlBuilder = $urlBuilder;
        return parent::__construct($context);
    }

    protected function _isAllowed() {
        return true;
    }

    public function execute()
    {
        $exceptionId = (int) $this->getRequest()->getParam('id');
        $this->_exceptionFactory->create()->load($exceptionId)->delete();

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }
}
