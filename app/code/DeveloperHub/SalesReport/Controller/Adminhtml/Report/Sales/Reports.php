<?php declare(strict_types=1);

namespace DeveloperHub\SalesReport\Controller\Adminhtml\Report\Sales;
use Magento\Reports\Controller\Adminhtml\Report\Sales;

class Reports extends Sales
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_initAction()->_setActiveMenu(
            'DeveloperHub_SalesReport::sales_report_grid'
        )->_addBreadcrumb(
            __('Sales Report'),
            __('Sales Report')
        );

        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Sales Report'));

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_sales_filter.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction([$gridBlock, $filterFormBlock]);

        $this->_view->renderLayout();
    }
}
