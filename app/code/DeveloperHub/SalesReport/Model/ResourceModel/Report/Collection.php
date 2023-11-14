<?php declare(strict_types=1);

namespace DeveloperHub\SalesReport\Model\ResourceModel\Report;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactory;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Sales\Model\ResourceModel\Report;
use Magento\Sales\Model\ResourceModel\Report\Collection\AbstractCollection;
use Psr\Log\LoggerInterface;

class Collection extends AbstractCollection
{
    /**
     * Selected columns
     *
     * @var array
     */
    protected $_selectedColumns = [];

    /**
     * Tables per period
     *
     * @var array
     */
    protected $salesOrderReportTable = [
        'order' => 'sales_order_reports_data',
    ];

    /**
     * @param EntityFactory $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param Report $resource
     * @param AdapterInterface|null $connection
     */
    public function __construct(
        EntityFactory $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        Report $resource,
        AdapterInterface $connection = null
    ) {
        $resource->init($this->getSalesOrderReportTable('order'));
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $resource,
            $connection
        );
    }

    /**
     * @return array|string[]
     */
    protected function _getSelectedColumns()
    {
        $this->_selectedColumns = [
            'entity_id' => 'entity_id',
            'created_at' => 'created_at',
            'invoice_created_at' => 'invoice_created_at',
            'order_increment_id' => 'order_increment_id',
            'invoice_number' => 'invoice_number',
            'sku' => 'sku',
            'customer_name' => 'customer_name',
            'customer_telephone' => 'customer_telephone',
            'status' => 'status',
            'grand_total' => 'grand_total',
            'vat_amount' => 'vat_amount',
            'payment_methods' => 'payment_methods'
        ];
        return $this->_selectedColumns;
    }

    /**
     * @param $order
     * @return mixed|string
     */
    public function getSalesOrderReportTable($order)
    {
        return $this->salesOrderReportTable[$order];
    }

    /**
     * @return $this|Collection
     */
    protected function _beforeLoad()
    {
        $cols = $this->_getSelectedColumns();
        if ($this->_from || $this->_to) {
            $mainTable = $this->getTable($this->getSalesOrderReportTable('order'));
            $this->getSelect()->from($mainTable, $cols);
            $this->getSelect()->where("created_at >= ?", $this->_from);
            $this->getSelect()->where("created_at <= ?", $this->_to);
            if (isset($this->_orderStatus)) {
                $this->getSelect()->where("status IN (?)", $this->_orderStatus);
            }
            return $this;
        }
        $mainTable = $this->getTable($this->getSalesOrderReportTable('order'));
        $this->getSelect()->from($mainTable, $cols);
        return parent::_beforeLoad();
    }
}
