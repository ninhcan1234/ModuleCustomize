<?php
namespace AHT\UiComponent\Model\Customer\Attribute;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory;
use Magento\Customer\Model\AttributeFactory;
use Magento\Customer\Model\ResourceModel\Attribute as AttributeResource;
use Magento\Eav\Model\Entity\Attribute as EavAttribute;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Store\Model\Store;


/**
 * Data provider for the form of adding new customer attribute.
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;
    protected $collection;
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    protected $loadedData;

    protected $dataPersistor;

    protected $request;

    protected $attribute;

    protected $attributeResource;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param StoreRepositoryInterface $storeRepository
     * @param ArrayManager $arrayManager
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        StoreRepositoryInterface $storeRepository,
        ArrayManager $arrayManager,
        array $meta = [],
        array $data = [],
        \Magento\Framework\App\RequestInterface $request,
        AttributeFactory $attribute,
        AttributeResource $attributeResource
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->storeRepository = $storeRepository;
        $this->arrayManager = $arrayManager;
        $this->request = $request;
        $this->attribute = $attribute;
        $this->attributeResource = $attributeResource;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $id = $this->request->getParam('id');
        $item = $this->attribute->create();
        $this->attributeResource->load($item, $id);
        
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
       
        $this->loadedData[$item->getId()] = $item->getData();

        if (!empty($data)) {
            $item = $this->collection->getNewEmptyItem();
            $item->setData($data);
            $this->loadedData[$item->getId()] = $item->getData();
        }

        return $this->loadedData;
    }

    /**
     * Get meta information
     *
     * @return array
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        $meta = $this->customizeAttributeCode($meta);
        $meta = $this->customizeFrontendLabels($meta);
        $meta = $this->customizeOptions($meta);

        return $meta;
    }

    /**
     * Customize attribute_code field
     *
     * @param array $meta
     * @return array
     */
    private function customizeAttributeCode($meta)
    {
        $meta['advanced_fieldset']['children'] = $this->arrayManager->set(
            'attribute_code/arguments/data/config',
            [],
            [
                'notice' => __(
                    'This is used internally. Make sure you don\'t use spaces or more than %1 symbols.',
                    EavAttribute::ATTRIBUTE_CODE_MAX_LENGTH
                ),
                'validation' => [
                    'max_text_length' => EavAttribute::ATTRIBUTE_CODE_MAX_LENGTH
                ]
            ]
        );
        return $meta;
    }

    /**
     * Customize frontend labels
     *
     * @param array $meta
     * @return array
     */
    private function customizeFrontendLabels($meta)
    {
        $labelConfigs = [];

        foreach ($this->storeRepository->getList() as $store) {
            $storeId = $store->getId();

            if (!$storeId) {
                continue;
            }
            $labelConfigs['frontend_label[' . $storeId . ']'] = $this->arrayManager->set(
                'arguments/data/config',
                [],
                [
                    'formElement' => Input::NAME,
                    'componentType' => Field::NAME,
                    'label' => $store->getName(),
                    'dataType' => Text::NAME,
                    'dataScope' => 'frontend_label[' . $storeId . ']'
                ]
            );
        }

        return $meta;
    }

    /**
     * Customize options
     *
     * @param array $meta
     * @return array
     */
    private function customizeOptions($meta)
    {
        $sortOrder = 1;
        foreach ($this->storeRepository->getList() as $store) {
            $storeId = $store->getId();
            $storeLabelConfiguration = [
                'dataType' => 'text',
                'formElement' => 'input',
                'component' => 'Magento_Catalog/js/form/element/input',
                'template' => 'Magento_Catalog/form/element/input',
                'prefixName' => 'option.value',
                'prefixElementName' => 'option_',
                'suffixName' => (string)$storeId,
                'label' => $store->getName(),
                'sortOrder' => $sortOrder,
                'componentType' => Field::NAME,
            ];
            // JS code can't understand 'required-entry' => false|null, we have to avoid even empty property.
            if ($store->getCode() == Store::ADMIN_CODE) {
                $storeLabelConfiguration['validation'] = [
                    'required-entry' => true,
                ];
            }
            
            ++$sortOrder;
        }

  
        return $meta;
    }
}
