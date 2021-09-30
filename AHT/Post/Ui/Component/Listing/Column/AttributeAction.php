<?php
namespace AHT\Post\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Escaper;

class AttributeAction extends Column
{
   /**
     * @var UrlInterface
     */
    protected $urlBuilder;
      /**
     * Url path
     */
    const URL_PATH_EDIT = 'uic/attrcustomer/edit';
    const URL_PATH_DELETE = 'uic/attrcustomer/delete';
        /**
     * @var Escaper
     */
    private $escaper;

 
    /**
     * Action constructor.
     * @param UrlInterface $urlBuilder
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        UrlInterface $urlBuilder,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
 
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['post_id'])) {
                    $name = $this->getEscaper()->escapeHtmlAttr($item['name']);
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_EDIT,
                                [
                                    'id' => $item['attribute_id'],
                                ]
                            ),
                            'label' => __('Edit'),
                            '__disableTmpl' => true,
                        ],
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_DELETE,
                                [
                                    'id' => $item['attribute_id'],
                                ]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'name' => __('Delete %1', $name),
                                'message' => __('Are you sure you want to delete a %1 record?', $name),
                            ],
                            'post' => true,
                            '__disableTmpl' => true,
                        ],
                    ];
                }
            }
        }

        return $dataSource;
    }

    /**
     * Get instance of escaper
     *
     * @return Escaper
     * @deprecated 101.0.7
     */
    private function getEscaper()
    {
        if (!$this->escaper) {
            $this->escaper = ObjectManager::getInstance()->get(Escaper::class);
        }
        return $this->escaper;
    }
}
