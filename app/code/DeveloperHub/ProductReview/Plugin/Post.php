<?php

namespace DeveloperHub\ProductReview\Plugin;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\ObjectManagerInterface;

class Post
{

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param ResourceConnection $resourceConnection
     * @param ResultFactory $resultFactory
     * @param DirectoryList $directoryList
     * @param File $file
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        ResultFactory $resultFactory,
        DirectoryList $directoryList,
        File $file,
        ObjectManagerInterface $objectManager
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->resultFactory = $resultFactory;
        $this->directoryList = $directoryList;
        $this->file = $file;
        $this->objectManager = $objectManager;
    }

    public function afterExecute(\Magento\Review\Controller\Product\Post $subject, $result)
    {
        $formData = $subject->getRequest()->getParams();
        $data = $subject->getRequest()->getFiles('file_upload');
        foreach ($data as $image) {
            $imageName[] = $image['name'];
        }
        $fileName['file_upload'] = implode(",", $imageName);
        $connection = $this->resourceConnection->getConnection();
        $tableName = $connection->getTableName('review_detail');
        $connection->update(
            $tableName,
            $fileName,
            $connection->quoteInto('title = ?', $formData['title'])
        );
        $this->saveFile($data);
        return $result;
    }

    public function saveFile($data)
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            if ($data) {
                foreach ($data as $image) {
                    $mediaDirectory = $this->objectManager->get('Magento\Framework\Filesystem')
                        ->getDirectoryWrite(DirectoryList::MEDIA);
                    $destinationDirectory = 'catalog/product/customimages/';
                    $destinationDirectoryPath = $mediaDirectory->getAbsolutePath($destinationDirectory) . $image['name'];
                    if (!is_dir($destinationDirectory)) {
                        mkdir($destinationDirectory, 0777, true);
                    }
                    if ($this->file->mv($image['tmp_name'], $destinationDirectoryPath)) {
                        $resultDataTmp = ['success' => true, 'file' => $destinationDirectoryPath, 'message' => __('File has been uploaded.')];
                    } else {
                        $resultDataTmp = ['error' => true, 'message' => 'File upload failed.'];
                    }
                }
            } else {
                $resultDataTmp = ['error' => true, 'message' => 'No file uploaded or an error occurred during upload.'];
            }
        } catch (\Exception $e) {
            $resultDataTmp = ['error' => true, 'message' => $e->getMessage()];
        }
        $result->setData($resultDataTmp);
        return $result;
    }
}
