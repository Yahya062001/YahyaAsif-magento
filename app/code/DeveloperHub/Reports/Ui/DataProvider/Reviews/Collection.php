<?php declare(strict_types=1);

namespace DeveloperHub\HidePrice\Ui\DataProvider\Reviews;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class Collection extends SearchResult
{
    protected function _initSelect()
    {
        $this->addFilterToMap('id', 'mainTable.id');
        parent::_initSelect();
    }
}
