<?php
namespace AHT\SaleAgent\Plugin;

use AHT\SaleAgent\Ui\DataProvider\SaleAgent\ListingDataProvider as SaleAgentDataProvider;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class AddAttributesToUiDataProvider
{
    /** @var AttributeRepositoryInterface */
    private $attributeRepository;

    /** @var ProductMetadataInterface */
    private $productMetadata;

    /**
     * Constructor
     *
     * @param \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     */
    public function __construct(
        AttributeRepositoryInterface $attributeRepository,
        ProductMetadataInterface $productMetadata
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->productMetadata = $productMetadata;
    }

    /**
     * Get Search Result after plugin
     *
     * @param \AHT\SaleAgent\Ui\DataProvider\SaleAgent\ListingDataProvider $subject
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

        $attribute = $this->attributeRepository->get('catalog_category', 'name');

        $result->getSelect()->joinLeft(
            ['customer_sale_agent_entity' => $attribute->getBackendTable()],
            'customer_sale_agent_entity.' . $column . ' = main_table.' . $column . ' AND customer_sale_agent_entity.entity_id = '
            . $attribute->getAttributeId(),
            ['commission_value' => 'customer_sale_agent_entity.commission_value']
        );

        $result->getSelect();

        return $result;
    }
}