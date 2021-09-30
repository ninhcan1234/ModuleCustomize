<?php
namespace AHT\SaleAgent\Ui\DataProvider\SaleAgent\Listing;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class Collection extends SearchResult
{
    /**
     * Override _initSelect to add custom columns
     *
     * @return void
     */
    protected function _initSelect()
    {
        $this->addFilterToMap('entity_id', 'main_table.entity_id');
        $this->addFilterToMap('entity_id', 'customer_sale_agent_entity.entity_id');
        parent::_initSelect();
    }
}