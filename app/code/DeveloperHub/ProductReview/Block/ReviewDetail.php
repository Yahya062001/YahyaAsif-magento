<?php

namespace DeveloperHub\ProductReview\Block;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;

class ReviewDetail extends Template
{
    public function __construct(
        Context $context,
        ResourceConnection $resourceConnection,
        StoreManagerInterface $storeManager
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @param $review
     * @return mixed
     */
    public function getFileUpload($review)
    {
        $tableName = $this->resourceConnection->getTableName('review_detail');
        $connection = $this->resourceConnection->getConnection();
        $reviewId = $review->getId();
        $select = $connection->select()
            ->from(
                ['c' => $tableName],
                ['*']
            )
            ->where(
                "c.review_id = ?",
                $reviewId
            );
        $records = $connection->fetchAll($select);
        $data = $records[0]['file_upload'];
        if (isset($data)) {
            return $data;
        }
        return null;
    }

    public function BaseUrl(){
        $url = $this->_storeManager->getStore()->getBaseUrl();
        return $url;
    }
}
