<?php declare(strict_types=1);

namespace DeveloperHub\SalesReport\Block\Adminhtml\Rewrite\Report\Filter;

use Magento\Sales\Block\Adminhtml\Report\Filter\Form\Order;

class FormExtended extends Order
{

    /**
     * @return $this|FormExtended
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $actionName = $this->getRequest()->getActionName();
        if ($actionName == 'reports') {
            $fieldset = $this->getForm()->getElement('base_fieldset');
            $fieldset->removeField('order_statuses');
            $statuses = $this->_orderConfig->create()->getStatuses();

            $values = [];
            foreach ($statuses as $code => $label) {
                $values[] = ['label' => __($label), 'value' => $code];
            }
            $fieldset->addField(
                'order_statuses',
                'multiselect',
                [
                    'name' => 'order_statuses',
                    'label' => '',
                    'values' => $values,
                    'display' => 'none'
                ],
                'show_order_statuses'
            );
        }
        return $this;
    }
}
