<?php declare(strict_types=1);

namespace DeveloperHub\SalesReport\Controller\Adminhtml\Report\Sales;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Data as BackendHelper;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Reports\Controller\Adminhtml\Report\Sales;

class CustomReportCsv extends Sales
{
    /**
     * @param Context $context
     * @param FileFactory $fileFactory
     * @param Date $dateFilter
     * @param TimezoneInterface $date
     * @param ResourceConnection $resourceConnection
     * @param TimezoneInterface $timezone
     * @param BackendHelper|null $backendHelperData
     */
    public function __construct(
        Context            $context,
        FileFactory        $fileFactory,
        Date               $dateFilter,
        TimezoneInterface  $date,
        ResourceConnection $resourceConnection,
        TimezoneInterface  $timezone,
        BackendHelper      $backendHelperData = null
    ) {
        $this->date =  $date;
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
        $fileName = 'report.csv';
        foreach ($csvData as $data) {
            $csv[] = [
                'Entity Id' => $data['entity_id'],
                'Order Created At' => $data['created_at'],
                'Invoice Created At' => $data['invoice_created_at'],
                'Order Increment Id' => $data['order_increment_id'],
                'Invoice Increment Id' => $data['invoice_number'],
                'Status' => $data['status'],
                'Grand Total' => $data['grand_total'],
                'Customer Name' => $data['customer_name'],
                'Customer Telephone' => $data['customer_telephone'],
                'Tax' => $data['vat_amount'],
                'Sku' => $data['sku'],
                'Payment Methods' => $data['payment_methods']
            ];
        }
        $headers = array_keys(reset($csv));
        $customCsv = '';
        $customCsv .= implode(',', $headers) . "\n";
        foreach ($csv as $row) {
            $customCsv .= implode(',', $row) . "\n";
        }
        $this->_response->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-Disposition', 'attachment; filename=' . $fileName)
            ->setHeader('Content-type', 'text/csv');
        $this->_response->setBody($customCsv);
        return $this->_response;
    }
}
