<?php

namespace DeveloperHub\SalesReport\Controller\Adminhtml\Report\Sales;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Data as BackendHelper;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Reports\Controller\Adminhtml\Report\Sales;

class CustomReportExcel extends Sales
{

    /**
     * @param Context $context
     * @param FileFactory $fileFactory
     * @param Date $dateFilter
     * @param ResourceConnection $resourceConnection
     * @param TimezoneInterface $timezone
     * @param BackendHelper|null $backendHelperData
     */
    public function __construct(
        Context            $context,
        FileFactory        $fileFactory,
        Date               $dateFilter,
        ResourceConnection $resourceConnection,
        TimezoneInterface  $timezone,
        BackendHelper      $backendHelperData = null
    ) {
        $this->date =  $timezone;
        $this->resourceConnection = $resourceConnection;
        parent::__construct(
            $context,
            $fileFactory,
            $dateFilter,
            $timezone,
            $backendHelperData
        );
    }

    public function execute()
    {
        $data = $this->getRequest()->getParam('filter');
        $decodedData = base64_decode($data);
        parse_str($decodedData, $parsedData);
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('sales_order_reports_data');
        $to = $this->date->date($parsedData['to'])->format('Y-m-d H:i:s');
        $from = $this->date->date($parsedData['from'])->format('Y-m-d H:i:s');
        if (isset($parsedData['order_statuses'])) {
            $select = $connection->select()->from($tableName)
                ->where('created_at >= ?', $from)
                ->where('created_at <= ?', $to)
                ->where('status IN (?)', $parsedData['order_statuses']);
        } else {
            $select = $connection->select()->from($tableName)
                ->where('created_at >= ?', $from)
                ->where('created_at <= ?', $to);
        }
        $csvData = $connection->fetchAll($select);
        $fileName = 'report.xml';
        foreach ($csvData as $data) {
            $csv[] = [
                'Entity_Id' => $data['entity_id'],
                'Order_Created_At' => $data['created_at'],
                'Invoice_Created_At' => $data['invoice_created_at'],
                'Order_Increment_Id' => $data['order_increment_id'],
                'Invoice_Increment_Id' => $data['invoice_number'],
                'Status' => $data['status'],
                'Grand_Total' => $data['grand_total'],
                'Customer_Name' => $data['customer_name'],
                'Customer_Telephone' => $data['customer_telephone'],
                'Tax' => $data['vat_amount'],
                'Sku' => $data['sku'],
                'Payment_Methods' => $data['payment_methods']
            ];
        }
        $xml = new \SimpleXMLElement('<data></data>');
        foreach ($csv as $row) {
            $item = $xml->addChild('item');
            foreach ($row as $key => $value) {
                $item->addChild($key, $value);
            }
        }
        $customXml = $xml->asXML();

        $this->_response->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-Disposition', 'attachment; filename=' . $fileName)
            ->setHeader('Content-type', 'text/xml');
        $this->_response->setBody($customXml);
        return $this->_response;
    }
}
