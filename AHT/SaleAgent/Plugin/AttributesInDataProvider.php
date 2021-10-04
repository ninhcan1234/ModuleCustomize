<?php
namespace AHT\SaleAgent\Plugin;

use AHT\SaleAgent\Ui\DataProvider\SaleAgent\ListingDataProvider as SaleAgentDataProvider;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use AHT\SaleAgent\Model\ResourceModel\SaleAgent\CollectionFactory as SaleAgentCollectionFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Catalog\Model\Product;

class AttributesInDataProvider
{
    /** @var AttributeRepositoryInterface */
    private $attributeRepository;

    /** @var ProductMetadataInterface */
    private $productMetadata;

    protected $saleAgentCollectionFactory;

    protected $eavAttribute;

    /**
    * Constructor
    *
    * @param \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository
    * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
    */
    public function __construct(
        Attribute $eavAttribute,
        AttributeRepositoryInterface $attributeRepository,
        ProductMetadataInterface $productMetadata,
        SaleAgentCollectionFactory $saleAgentCollectionFactory
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->productMetadata = $productMetadata;
        $this->saleAgentCollectionFactory = $saleAgentCollectionFactory;
        $this->eavAttribute = $eavAttribute;
    }

    /**
     * Get Search Result after plugin
     *
     * @param \Dev\Grid\Ui\DataProvider\Category\ListingDataProvider $subject
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult $result
     * @return \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
     */
    public function afterGetSearchResult(SaleAgentDataProvider $subject, SearchResult $result)
    {
        if ($result->isLoaded()) {
            return $result;
        }

        $edition = $this->productMetadata->getEdition();

        $column = 'entity_id';

        if ($edition == 'Enterprise') {
            $column = 'row_id';
        }

        $saleAgentCollection = $this->saleAgentCollectionFactory->create();
        $saleAgentCollection->getSelect()
            ->joinInner(
                ['so' => 'sales_order'],
                "main_table.order_id = so.increment_id"
            )
            ->joinLeft(
                ['soi' => 'sales_order_item'],
                "main_table.order_item_sku = soi.sku and main_table.order_item_id = soi.product_id"
            )
            ->joinLeft(
                ['cped' => 'catalog_product_entity_decimal'],
                "main_table.commission_value = cped.value 
                    and soi.product_id = cped.entity_id 
                    and cped.attribute_id = '{$this->getIdProductEavEntity('commission_value')}'"
            );

        return $saleAgentCollection;
    }
    
    private function getIdProductEavEntity($code)
    {
        return $this->eavAttribute->getIdByCode(Product::ENTITY, $code);
    }
}