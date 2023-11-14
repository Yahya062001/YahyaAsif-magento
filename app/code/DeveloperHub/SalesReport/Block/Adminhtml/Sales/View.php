<?php declare(strict_types=1);

namespace DeveloperHub\SalesReport\Block\Adminhtml\Sales;

use Magento\Backend\Block\Widget\Grid\Container;

class View extends Container
{
    protected $_template = 'Magento_Reports::report/grid/container.phtml';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_blockGroup = 'DeveloperHub_SalesReport';
        $this->_controller = 'adminhtml_sales_filter';
        $this->_headerText = __('Sales Report');
        parent::_construct();

        $this->buttonList->remove('add');
        $this->addButton(
            'filter_form_submit',
            ['label' => __('Show Report'), 'onclick' => 'filterFormSubmit()', 'class' => 'primary']
        );
    }

    /**
     * Get filter URL
     *
     * @return string
     */
    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/reports', ['_current' => true]);
    }
}
