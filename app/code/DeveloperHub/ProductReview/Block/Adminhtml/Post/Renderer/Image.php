<?php
namespace DeveloperHub\ProductReview\Block\Adminhtml\Post\Renderer;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Escaper;
use Magento\Store\Model\StoreManagerInterface;

class Image extends AbstractElement
{
    const DESTINATION_DIRECTORY = 'catalog/product/customimages/';
    /**
     * @var StoreManagerInterface
     */
    private $_storeManager;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param StoreManagerInterface $storemanager
     * @param Escaper $escaper
     * @param Factory $factoryElement
     * @param CollectionFactory $collectionFactory
     * @param DirectoryList $directoryList
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        StoreManagerInterface $storemanager,
        Escaper $escaper,
        Factory $factoryElement,
        CollectionFactory $collectionFactory,
        DirectoryList $directoryList,
        array $data = []
    ) {
        $this->_storeManager = $storemanager;
        $this->directoryList = $directoryList;
        parent::__construct($factoryElement, $collectionFactory, $escaper);
    }

    /**
     * @return array|string|null
     */
    public function getElementHtml()
    {
        $html = '';

        if ($this->getValue()) {
            $html = $this->getMediaImageHtml($this->getValue());
        }
        return $html;
    }
    public function getMediaImageHtml($imagesName)
    {
        if ($imagesName) {
            $dataArray = explode(",", $imagesName);
            foreach ($dataArray as $imageName) {
                $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
                $destinationDirectory = self::DESTINATION_DIRECTORY;
                $imageUrls[] = $baseUrl . 'media/' . $destinationDirectory . $imageName;
            }
            $html = "<p style='font-size: large; text-align: center;'>Review Media</p strong>";
            $html .= "<div style='display: flex; justify-content: center;'>";
            foreach ($imageUrls as $index => $imageUrl) {
                $html .= "<a href='$imageUrl'  data-caption='Image $index'><img src='$imageUrl' height='250px' width='250px' class='fancybox'></a>";
            }
            $html .= "</div><br>";
            return $html;
        }
        return null;
    }
}

