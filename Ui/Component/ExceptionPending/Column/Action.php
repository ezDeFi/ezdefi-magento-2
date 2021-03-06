<?php

namespace Ezdefi\Payment\Ui\Component\ExceptionPending\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class Action extends Column
{
    /** Url path */
    const URL_DELETE_EXCEPTION = 'adminhtml/exception/delete';
    const URL_CONFIRM_PAID     = 'adminhtml/exception/confirmpaid';
    /** @var UrlInterface */
    protected $_urlBuilder;

    /**
     * @var string
     */
    private $_editUrl;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     * @param string $editUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    )
    {
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item['id'])) {
                    $item[$name]['delete'] = [
                        'href'    => $this->_urlBuilder->getUrl(
                            self::URL_DELETE_EXCEPTION,
                            [
                                'id' => $item['id']
                            ]
                        ),
                        'label'   => __('Delete'),
                        'confirm' => [
                            'title'   => __('Delete Exception'),
                            'message' => __('Are you sure you want to delete this record?')
                        ]
                    ];
                    if($item['order_id']) {
                        $item[$name]['confirm'] = [
                            'href'    => $this->_urlBuilder->getUrl(
                                self::URL_CONFIRM_PAID,
                                [
                                    'id' => $item['id']
                                ]
                            ),
                            'label'   => __('Confirm'),
                            'confirm' => [
                                'title'   => __('Confirm Exception'),
                                'message' => __('Are you sure you want to confirm this order?')
                            ]
                        ];
                    }
                }
            }
        }

        return $dataSource;
    }
}
