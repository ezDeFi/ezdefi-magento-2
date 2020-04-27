<?php
namespace Ezdefi\Payment\Model\ResourceModel\Exception\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\Search\AggregationInterface;
use Ezdefi\Payment\Model\ResourceModel\Exception\Collection as EntityCollection;
use \Magento\Framework\Webapi\Rest\Request;

class Collection extends EntityCollection implements SearchResultInterface
{
    public static $table = 'ezdefi_exception';

    protected $aggregations;
    protected $_request;

    public function __construct(
        Request $request,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        $model = 'Magento\Framework\View\Element\UiComponent\DataProvider\Document',
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
        $this->_request = $request;
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);
    }

    /**
     * @return AggregationInterface
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * @param AggregationInterface $aggregations
     * @return $this
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * Set search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * Set items list.
     *
     * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setItems(array $items = null)
    {
        return $this;
    }

    public function afterSearch($intercepter, $collection)
    {
        if ($collection->getMainTable() === $collection->getConnection()->getTableName(self::$table)) {

            $collection->getSelect()->addFieldToFilter('currency', 'eth');

            $where = $collection->getSelect()->getPart(\Magento\Framework\DB\Select::WHERE);
            echo $collection->getSelect()->__toString();die;
        }
        return $collection;
    }

    protected function _renderFiltersBefore()
    {
        $request = $this->_request->getParams();

        $this->addFieldToFilter('order_assigned', array('null' => true));
        $this->addFieldToFilter('explorer_url', array('neq' => 'NULL'));

        if(isset($request['filters']['increment_id'])) {
            $incrementId = $request['filters']['increment_id'];
            $this->addFieldToFilter('od.increment_id', $incrementId);
        }

        if(isset($request['filters']['amount_id'])) {
            $amount = $request['filters']['amount_id'];
            $this->addFieldToFilter('amount_id', ['like' => $amount.'%'])->setOrder('`amount_id`', 'ASC');
        } else {
            $this->setOrder('`id`', 'DESC');
        }

        parent::_renderFiltersBefore();
    }
}