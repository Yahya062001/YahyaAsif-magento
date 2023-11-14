<?php
namespace EgSolution\ShoppingCart\Block;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\View\Element\Template;

class SimpleProducts extends Template
{


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
     * @param Configurable $configurable
     * @param ProductRepositoryInterface $productRepositoryInterface
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Configurable $configurable,
        ProductRepositoryInterface $productRepositoryInterface,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configurable = $configurable;
        $this->productRepositoryInterface = $productRepositoryInterface;
    }

    public function getSimpleProduct($productId, $typeId)
    {
        try {
            if ($typeId == 'configurable') {
                $childProductsId = $this->configurable->getChildrenIds($productId);
                foreach ($childProductsId[0] as $childProductId) {
                    $childProduct = $this->productRepositoryInterface->getById($childProductId);
                    $name[] = $childProduct->getName();
                }
                return $name;
            }
        } catch (\Exception $e) {
            return null;
        }
    }
}
