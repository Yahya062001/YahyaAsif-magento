<?php declare(strict_types=1);

namespace DeveloperHub\SalesReport\Block\Adminhtml\Renderer;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\OrderFactory;

class OrderLink extends AbstractRenderer
{
    /**
     * @param Context $context
     * @param UrlInterface $urlBuilder
     * @param OrderFactory $orderFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        UrlInterface $urlBuilder,
        OrderFactory $orderFactory,
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->orderFactory = $orderFactory;
        parent::__construct($context, $data);
    }

    /**
     * @param DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        $order = $this->orderFactory->create()->loadByIncrementId($value);
        $orderId = $order->getId();
        $url = $this->urlBuilder->getUrl(
            'sales/order/view',
            ['order_id' => $orderId]
        );
        return '<a href="' . $url . '">' . $value . '</a>';
    }
}
