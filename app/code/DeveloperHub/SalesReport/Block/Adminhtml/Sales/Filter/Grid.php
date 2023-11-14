<?php declare(strict_types=1);

namespace DeveloperHub\SalesReport\Block\Adminhtml\Sales\Filter;

use DeveloperHub\SalesReport\Model\ResourceModel\Report\Collection;
use Magento\Reports\Block\Adminhtml\Grid\AbstractGrid;

class Grid extends AbstractGrid
{
    protected $_columnGroupBy = 'entity_id';

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setCountTotals(false);
    }

    public function getResourceCollectionName()
    {
        return Collection::class;
    }
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('Entity Id'),
                'index' => 'entity_id',
                'type' => 'int',
                'sortable' => false,
                'header_css_class' => 'col-product',
                'column_css_class' => 'col-product'
            ]
        );

        $this->addColumn(
            'created_at',
            [
                'header' => __('Order Created At'),
                'index' => 'created_at',
                'type' => 'string',
                'sortable' => false,
                'header_css_class' => 'col-product',
                'column_css_class' => 'col-product'
            ]
        );

        $this->addColumn(
            'invoice_created_at',
            [
                'header' => __('Invoice Created At'),
                'index' => 'invoice_created_at',
                'type' => 'string',
                'sortable' => false,
                'header_css_class' => 'col-product',
                'column_css_class' => 'col-product'
            ]
        );

        $this->addColumn(
            'order_increment_id',
            [
                'header' => __('Order Increment Id'),
                'index' => 'order_increment_id',
                'type' => 'string',
                'sortable' => false,
                'header_css_class' => 'col-product',
                'column_css_class' => 'col-product',
                'renderer' => 'DeveloperHub\SalesReport\Block\Adminhtml\Renderer\OrderLink'
            ]
        );

        $this->addColumn(
            'invoice_number',
            [
                'header' => __('Invoice Increment Id'),
                'index' => 'invoice_number',
                'type' => 'string',
                'sortable' => false,
                'header_css_class' => 'col-product',
                'column_css_class' => 'col-product',
                'renderer' => 'DeveloperHub\SalesReport\Block\Adminhtml\Renderer\InvoiceLink'
            ]
        );
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'string',
                'sortable' => false,
                'header_css_class' => 'col-product',
                'column_css_class' => 'col-product'
            ]
        );

        $this->addColumn(
            'grand_total',
            [
                'header' => __('Grand Total'),
                'index' => 'grand_total',
                'type' => 'string',
                'sortable' => false,
                'header_css_class' => 'col-product',
                'column_css_class' => 'col-product'
            ]
        );
        $this->addColumn(
            'customer_name',
            [
                'header' => __('Customer Name'),
                'index' => 'customer_name',
                'type' => 'string',
                'sortable' => false,
                'header_css_class' => 'col-product',
                'column_css_class' => 'col-product'
            ]
        );
        $this->addColumn(
            'customer_telephone',
            [
                'header' => __('Customer Telephone'),
                'index' => 'customer_telephone',
                'type' => 'string',
                'sortable' => false,
                'header_css_class' => 'col-product',
                'column_css_class' => 'col-product'
            ]
        );

        $this->addColumn(
            'vat_amount',
            [
                'header' => __('Tax'),
                'index' => 'vat_amount',
                'type' => 'string',
                'sortable' => false,
                'header_css_class' => 'col-product',
                'column_css_class' => 'col-product'
            ]
        );

        $this->addColumn(
            'sku',
            [
                'header' => __('Sku'),
                'type' => 'string',
                'index' => 'sku',
                'sortable' => false,
                'header_css_class' => 'col-product',
                'column_css_class' => 'col-product'
            ]
        );

        $this->addColumn(
            'payment_methods',
            [
                'header' => __('Payment Method'),
                'index' => 'payment_methods',
                'type' => 'string',
                'sortable' => false,
                'header_css_class' => 'col-product',
                'column_css_class' => 'col-product'
            ]
        );
        $this->addExportType('*/*/CustomReportCsv', __('CSV'));
        $this->addExportType('*/*/CustomReportExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }
}
