<?php

namespace AHT\UiComponent\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class ActionAttribute extends Column
{
    /** Url path */
    const URL_PATH_EDIT = 'uic/attrcustomer/edit';
    const URL_PATH_DELETE = 'uic/attrcustomer/delete';
    /** @var UrlInterface */
    protected $_urlBuilder;

    /**
     * @var string
     */
    private $_editUrl;

    private $_deleteUrl;

    protected $eavSetupFactory;

    protected $moduleDataSetup;

    /**
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface       $urlBuilder
     * @param array              $components
     * @param array              $data
     * @param string             $editUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        $editUrl = self::URL_PATH_EDIT,
        $deleteUrl = self::URL_PATH_DELETE
    ) {
        $this->_urlBuilder = $urlBuilder;
        $this->_editUrl = $editUrl;
        $this->_deleteUrl = $deleteUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item['attribute_id'])) {
                    $item[$name]['edit'] = [
                        'href' => $this->_urlBuilder->getUrl(
                            $this->_editUrl,
                            ['id' => $item['attribute_id']]
                        ),
                        'label' => __('Edit'),
                    ];
                    $item[$name]['delete'] = [
                        'href' => $this->_urlBuilder->getUrl(
                            $this->_deleteUrl,
                            ['id' => $item['attribute_id']]
                        ),
                        'label' => __('Delete'),
                        'confirm' => [
                            'name' => __('Delete %1', $name),
                            'message' => __('Are you sure you want to delete a %1 record?', $name),
                        ],
                        'post' => true,
                        '__disableTmpl' => true,
                    ];
                }
            }
        }

        return $dataSource;
    }
}
