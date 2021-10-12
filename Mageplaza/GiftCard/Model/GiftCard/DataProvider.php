<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mageplaza\GiftCard\Model\GiftCard;

use Mageplaza\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Framework\App\RequestInterface;
use Mageplaza\GiftCard\Helper\Data as DataConfig;

/**
 * Class DataProvider
 */
class DataProvider extends \Magento\Ui\DataProvider\ModifierPoolDataProvider
{
    /**
     * @var \Mageplaza\GiftCard\Model\ResourceModel\GiftCard\Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    protected $request;

    protected $dataConfig;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $giftCardCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $pool
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $giftCardCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null,
        RequestInterface $request,
        DataConfig $dataConfig
    ) {
        $this->dataConfig = $dataConfig;
        $this->request = $request;
        $this->collection = $giftCardCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var \Mageplaza\GiftCard\Model\GiftCard $giftCard */
        foreach ($items as $giftCard) {
            $this->loadedData[$giftCard->getId()] = $giftCard->getData();
        }
        // $this->loadedData['code_length' => $this->dataConfig->getSystemConfig('giftcard/code_config/code_length')];

        $data = $this->dataPersistor->get('giftcard_giftcard');
        if (!empty($data)) {
            $giftCard = $this->collection->getNewEmptyItem();
            $giftCard->setData($data);
            $this->loadedData[$giftCard->getId()] = $giftCard->getData();
            $this->dataPersistor->clear('giftcard_giftcard');
        }

        return $this->loadedData;
    }

    public function getMeta()
    {
        $meta = parent::getMeta();
        $id = $this->request->getParam('id');
        if($id === null){
            $meta['gift']['children']['code_length']['arguments']['data']['config']['disabled'] = false;
            $meta['gift']['children']['create_from']['arguments']['data']['config']['disabled'] = false;
        }
        else{
            $meta['gift']['children']['code_length']['arguments']['data']['config']['disabled'] = true;
            $meta['gift']['children']['create_from']['arguments']['data']['config']['disabled'] = true;
        }

        return $meta;
    }
}
