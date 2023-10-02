<?php
namespace DeveloperHub\StockStatus\Block;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\GroupedProduct\Model\Product\Type\Grouped;

class StockInformation extends Template
{
    /**
     * @var StockItemRepository
     */
    private $stockItemRepository;

    /**
     * @var Http
     */
    private $request;

    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepositoryInterface;

    /**
     * @var Configurable
     */
    private $configurable;


    /**
     * @param Template\Context $context
     * @param StockItemRepository $stockItemRepository
     * @param CollectionFactory $productCollectionFactory
     * @param Configurable $configurable
     * @param ProductRepositoryInterface $productRepositoryInterface
     * @param Http $request
     * @param AttributeRepositoryInterface $attributeRepository
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        StockItemRepository $stockItemRepository,
        CollectionFactory $productCollectionFactory,
        Configurable $configurable,
        ProductRepositoryInterface $productRepositoryInterface,
        Http $request,
        AttributeRepositoryInterface $attributeRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->stockItemRepository = $stockItemRepository;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->configurable = $configurable;
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->_request = $request;
        $this->attributeRepository = $attributeRepository;
    }

    public function getStockInfo($productId, $sku, $typeId)
    {
        try {
            if ($typeId == 'configurable') {
                $configurableProduct = $this->productRepositoryInterface->get($sku);
                $childProductsId = $this->configurable->getChildrenIds($configurableProduct->getId());
                foreach ($childProductsId[0] as $childProductId) {
                    $childProduct = $this->productRepositoryInterface->getById($childProductId);
                    $colorLabel = $this->getColorLabel($childProduct);
                    $sizeLabel = $this->getSizeLabel($childProduct);
                    $id = $childProduct->getId();
                    $item = $this->stockItemRepository->get($id);
                    $qty[] = [
                        'qty' => $item->getQty(),
                        'name' => $childProduct->getName(),
                        'color' => $colorLabel,
                        'size' => $sizeLabel
                        ];
                }
                return $qty;
            } else {
                $stockItem = $this->stockItemRepository->get($productId);
                $product = $this->productRepositoryInterface->get($sku);
                if ($this->_request->getFullActionName() != 'catalog_product_view') {
                    return $stockItem->getQty();
                } else {
                    $qty[] = [
                        'qty' => $stockItem->getQty(),
                        'name' => $product->getName(),
                        'color' => $this->getColorLabel($product),
                        'size' => $this->getSizeLabel($product)
                    ];
                    return $qty;
                }
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getRelatedProducts($productId)
    {
        $stockItem = $this->stockItemRepository->get($productId);
        return $stockItem->getQty();
    }
    public function getHttpRequest()
    {
        return $this->_request->getFullActionName();
    }

    /**
     * @param $childProduct
     * @return mixed|void
     * @throws NoSuchEntityException
     */
    public function getColorLabel($childProduct)
    {
        $attributeColor = $this->attributeRepository->get(Product::ENTITY, 'color');
        $colorAttribute = $childProduct->getColor();
        $label = $attributeColor->getSource()->getOptionText($colorAttribute);
        return $label;
    }

    /**
     * @param $childProduct
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getSizeLabel($childProduct)
    {
        $attributeSize = $this->attributeRepository->get(Product::ENTITY, 'size');
        $sizeAttribute = $childProduct->getSize();
        $label = $attributeSize->getSource()->getOptionText($sizeAttribute);
        return $label;
    }

    /**
     * @param $sku
     * @return array|void
     * @throws NoSuchEntityException
     */
    public function getGroupedProduct($sku)
    {
        $groupedProduct = $this->productRepositoryInterface->get($sku);
        if ($groupedProduct->getTypeId() === Grouped::TYPE_CODE) {
            $associatedProducts = $groupedProduct->getTypeInstance()->getAssociatedProducts($groupedProduct);

            foreach ($associatedProducts as $associatedProduct) {
                $associatedProductId = $associatedProduct->getId();
                $stockInfo = $this->stockItemRepository->get($associatedProductId);
                $associatedProductQty[] = [
                    'qty' => $stockInfo->getQty(),
                    'name' => $associatedProduct->getName()
                    ];
            }
            return $associatedProductQty;
        }
    }

    /**
     * @param $sku
     * @return array|void
     * @throws NoSuchEntityException
     */
    public function getBundleProduct($sku)
    {
        $bundleProduct = $this->productRepositoryInterface->get($sku);

        if ($bundleProduct->getTypeId() === 'bundle') {
            $optionCollection = $bundleProduct->getTypeInstance()->getOptionsCollection($bundleProduct);
            $selectionCollection = $bundleProduct->getTypeInstance()->getSelectionsCollection(
                $bundleProduct->getTypeInstance()->getOptionsIds($bundleProduct),
                $bundleProduct
            );

            foreach ($optionCollection as $option) {
                foreach ($selectionCollection as $selection) {
                    if ($option->getOptionId() == $selection->getOptionId()) {
                        $productId = $selection->getId();
                        $stockInfo = $this->stockItemRepository->get($productId);
                        $associatedProductQty[] = [
                            'qty' => $stockInfo->getQty(),
                            'name' => $selection->getName()
                        ];
                    }
                }
            }
            return $associatedProductQty;
        }
    }
}
