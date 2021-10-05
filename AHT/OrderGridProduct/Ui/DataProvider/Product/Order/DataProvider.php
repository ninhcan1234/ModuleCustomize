<?php
namespace AHT\OrderGridProduct\Ui\DataProvider\Product\Order;

use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory;
use Magento\Review\Model\ResourceModel\Review\Product\Collection;
use Magento\Sales\Model\Review;
use Magento\Sales\Model\Order\Item;
use Magento\Catalog\Model\ProductFactory;

class DataProvider extends AbstractDataProvider    
{
    /**
     * @var CollectionFactory
     * @since 100.1.0
     */
    protected $collectionFactory;

    /**
     * @var RequestInterface
     * @since 100.1.0
     */
    protected $request;

    protected $_productFactory;
    

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        ProductFactory $productFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collectionFactory = $collectionFactory;
        $this->collection = $this->collectionFactory->create();
        $this->request = $request;
        $this->_productFactory = $productFactory;
    }

    /**
     * {@inheritdoc}
     * @since 100.1.0
     */
    public function getData()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('Magento\Framework\App\Request\Http');
        $request = $request->getServer('HTTP_REFERER');
        $request = explode('/',$request);
        $id = $request[9];

        $this->getCollection()->getSelect()
        ->join(
            ['s' => 'sales_order'],
            'main_table.order_id = s.entity_id'
        )
        ->join(
            ['sgw' => 'sales_order_grid'],
            's.entity_id = sgw.entity_id',
            ['sgw.shipping_name', 'billing_name']
        )
        ->where('main_table.product_id = ?' , $id);
        
        $arrItems = [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => [],
        ];

        foreach ($this->getCollection() as $item) {
            $arrItems['items'][] = $item->toArray([]);
        }

        return $arrItems;
    }


    /**
     * {@inheritdoc}
     * @since 100.1.0
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        $field = $filter->getField();

        if (in_array($field, ['review_id', 'created_at', 'status_id'])) {
            $filter->setField('rt.' . $field);
        }

        if (in_array($field, ['title', 'nickname', 'detail'])) {
            $filter->setField('rdt.' . $field);
        }

        if ($field === 'review_created_at') {
            $filter->setField('rt.created_at');
        }

        parent::addFilter($filter);
    }
}