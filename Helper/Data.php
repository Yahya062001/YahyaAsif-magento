<?php declare(strict_types=1);

namespace DeveloperHub\StockStatus\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_PATH_CONFIG_ENABLE = 'stock_status/stock_status_information/is_enabled';

    const PRODUCT_LISTING_PAGES = 'stock_status/stock_status_information/plp';

    const LINK = 'stock_status/stock_status_information/link';



    /**
     * @param $storeId
     * @return mixed
     */
    public function getConfigFieldEnable($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CONFIG_ENABLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    public function getPlp($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::PRODUCT_LISTING_PAGES,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    public function getLink($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::LINK,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
