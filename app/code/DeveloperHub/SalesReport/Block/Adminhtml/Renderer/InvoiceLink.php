<?php declare(strict_types=1);

namespace DeveloperHub\SalesReport\Block\Adminhtml\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Backend\Block\Context;
use Magento\Framework\DataObject;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order\Invoice;

class InvoiceLink extends AbstractRenderer
{
    /**
     * @param Context $context
     * @param UrlInterface $urlBuilder
     * @param Invoice $invoice
     * @param array $data
     */
    public function __construct(
        Context $context,
        UrlInterface $urlBuilder,
        Invoice $invoice,
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->invoice = $invoice;
        parent::__construct($context, $data);
    }

    /**
     * @param DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        $invoice = $this->invoice->loadByIncrementId($value);
        $invoiceId = $invoice->getId();
        $url = $this->urlBuilder->getUrl(
            'sales/order_invoice/view',
            ['invoice_id' => $invoiceId]
        );
        return '<a href="' . $url . '">' . $value . '</a>';
    }
}
