<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ProductFeed
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductFeed\Helper;

use DateTimeZone;
use Exception;
use Liquid\Tag\TagFor;
use Liquid\Template;
use Liquid\Variable;
use Magento\Backend\Model\Session;
use Magento\Backend\Model\UrlInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\Config\Model\ResourceModel\Config as ModelConfig;
use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory as ConfigCollectionFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\DataObject;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem\Driver\File as DriverFile;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Filesystem\Io\Ftp;
use Magento\Framework\Filesystem\Io\Sftp;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Url as UrlAbstract;
use Magento\Review\Model\Review;
use Magento\Review\Model\Review\SummaryFactory;
use Magento\Review\Model\ReviewFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Mageplaza\Core\Helper\AbstractData as CoreHelper;
use Mageplaza\ProductFeed\Block\Adminhtml\LiquidFilters;
use Mageplaza\ProductFeed\Model\Config\Source\Delivery;
use Mageplaza\ProductFeed\Model\Config\Source\Events;
use Mageplaza\ProductFeed\Model\Config\Source\Status;
use Mageplaza\ProductFeed\Model\Feed;
use Mageplaza\ProductFeed\Model\FeedFactory;
use Mageplaza\ProductFeed\Model\HistoryFactory;
use phpseclib\Crypt\RSA;
use RuntimeException;
use phpseclib\Net\SFTP as libSFTP;
use Zend_Http_Client;
use Zend_Http_Response;

/**
 * Class Data
 * @package Mageplaza\ProductFeed\Helper
 */
class Data extends CoreHelper
{
    const CONFIG_MODULE_PATH    = 'product_feed';
    const XML_PATH_EMAIL        = 'email';
    const FEED_FILE_PATH        = BP . '/pub/media/mageplaza/feed/';
    const FEED_KEY_FILE_PATH    = BP . '/pub/media/mageplaza/feed/key_file/';
    const GOOGLE_SHOPPING_URL   = 'https://shoppingcontent.googleapis.com/content/v2.1/';
    const GOOGLE_SHOPPING_TOKEN = 'https://oauth2.googleapis.com/token';

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var LiquidFilters
     */
    protected $liquidFilters;

    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var Ftp
     */
    protected $ftp;

    /**
     * @var Sftp
     */
    protected $sftp;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var HistoryFactory
     */
    protected $historyFactory;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var ReviewFactory
     */
    protected $reviewFactory;

    /**
     * @var SummaryFactory
     */
    protected $reviewSummaryFactory;

    /**
     * @var FeedFactory
     */
    protected $feedFactory;

    /**
     * @var Resolver
     */
    protected $resolver;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @var UrlInterface
     */
    protected $backendUrl;

    /**
     * @var StockRegistryInterface
     */
    protected $stockState;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var CollectionFactory
     */
    protected $prdAttrCollectionFactory;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var DriverFile
     */
    protected $driverFile;

    /**
     * @var UrlAbstract
     */
    protected $urlModel;

    /**
     * @var UrlFinderInterface
     */
    protected $urlFinder;

    /**
     * @var CurlFactory
     */
    protected $curlFactory;

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var ModelConfig
     */
    protected $modelConfig;

    /**
     * @var Mapping
     */
    protected $helperMapping;

    /**
     * @var ConfigCollectionFactory
     */
    protected $configCollectionFactory;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param ProductFactory $productFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param UrlInterface $backendUrl
     * @param Ftp $ftp
     * @param Sftp $sftp
     * @param ManagerInterface $messageManager
     * @param TransportBuilder $transportBuilder
     * @param DateTime $date
     * @param TimezoneInterface $timezone
     * @param Resolver $resolver
     * @param File $file
     * @param ReviewFactory $reviewFactory
     * @param SummaryFactory $reviewSummaryFactory
     * @param StockRegistryInterface $stockState
     * @param LiquidFilters $liquidFilters
     * @param HistoryFactory $historyFactory
     * @param FeedFactory $feedFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @param CollectionFactory $prdAttrCollectionFactory
     * @param UrlFinderInterface $urlFinder
     * @param Session $session
     * @param DriverFile $driverFile
     * @param UrlAbstract $urlModel
     * @param CurlFactory $curlFactory
     * @param EncryptorInterface $encryptor
     * @param ModelConfig $modelConfig
     * @param Mapping $helperMapping
     * @param ConfigCollectionFactory $configCollectionFactory
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        ProductFactory $productFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        UrlInterface $backendUrl,
        Ftp $ftp,
        Sftp $sftp,
        ManagerInterface $messageManager,
        TransportBuilder $transportBuilder,
        DateTime $date,
        TimezoneInterface $timezone,
        Resolver $resolver,
        File $file,
        ReviewFactory $reviewFactory,
        SummaryFactory $reviewSummaryFactory,
        StockRegistryInterface $stockState,
        LiquidFilters $liquidFilters,
        HistoryFactory $historyFactory,
        FeedFactory $feedFactory,
        PriceCurrencyInterface $priceCurrency,
        CollectionFactory $prdAttrCollectionFactory,
        UrlFinderInterface $urlFinder,
        Session $session,
        DriverFile $driverFile,
        UrlAbstract $urlModel,
        CurlFactory $curlFactory,
        EncryptorInterface $encryptor,
        ModelConfig $modelConfig,
        Mapping $helperMapping,
        ConfigCollectionFactory $configCollectionFactory
    ) {
        $this->productFactory            = $productFactory;
        $this->file                      = $file;
        $this->liquidFilters             = $liquidFilters;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->ftp                       = $ftp;
        $this->sftp                      = $sftp;
        $this->messageManager            = $messageManager;
        $this->date                      = $date;
        $this->historyFactory            = $historyFactory;
        $this->transportBuilder          = $transportBuilder;
        $this->reviewFactory             = $reviewFactory;
        $this->reviewSummaryFactory      = $reviewSummaryFactory;
        $this->feedFactory               = $feedFactory;
        $this->resolver                  = $resolver;
        $this->timezone                  = $timezone;
        $this->backendUrl                = $backendUrl;
        $this->stockState                = $stockState;
        $this->priceCurrency             = $priceCurrency;
        $this->prdAttrCollectionFactory  = $prdAttrCollectionFactory;
        $this->session                   = $session;
        $this->driverFile                = $driverFile;
        $this->urlModel                  = $urlModel;
        $this->urlFinder                 = $urlFinder;
        $this->curlFactory               = $curlFactory;
        $this->encryptor                 = $encryptor;
        $this->modelConfig               = $modelConfig;
        $this->helperMapping             = $helperMapping;
        $this->configCollectionFactory   = $configCollectionFactory;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * Convert to local time
     *
     * @param string $time
     *
     * @return \DateTime|string
     * @throws Exception
     */
    public function convertToLocaleTime($time)
    {
        $localTime = new \DateTime($time, new DateTimeZone('UTC'));
        $localTime->setTimezone(new DateTimeZone($this->timezone->getConfigTimezone()));
        $localTime = $localTime->format('Y-m-d H:i:s');

        return $localTime;
    }

    /**
     * Get email config
     *
     * @param string $code
     * @param int $storeId
     *
     * @return mixed
     */
    public function getEmailConfig($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getModuleConfig(self::XML_PATH_EMAIL . $code, $storeId);
    }

    /**
     * Send mail
     *
     * @param array $sendTo
     * @param string $mes
     * @param string $emailTemplate
     * @param int $storeId
     *
     * @return bool
     * @throws Exception
     */
    public function sendMail($sendTo, $mes, $emailTemplate, $storeId)
    {
        if (!isset($sendTo)) {
            $this->messageManager->addErrorMessage(__('Please enter the email before send.'));

            return false;
        }
        try {
            $sendTo = array_map('trim', $sendTo);
            $this->transportBuilder
                ->setTemplateIdentifier($emailTemplate)
                ->setTemplateOptions([
                    'area'  => Area::AREA_FRONTEND,
                    'store' => $storeId,
                ])
                ->setTemplateVars([
                    'viewLogUrl' => $this->backendUrl->getUrl('mpproductfeed/logs/'),
                    'mes'        => $mes
                ])
                ->setFrom('general');
            foreach ($sendTo as $email) {
                $this->transportBuilder->addTo($email);
            }
            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();

            return true;
        } catch (MailException $e) {
            $this->_logger->critical($e->getLogMessage());
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__(
                'Something went wrong while sending Email. %1',
                $e->getMessage()
            ));
        }

        return false;
    }

    /**
     * Generate and delivery Feed
     *
     * @param Feed $feed
     * @param bool $forceGenerate
     * @param bool $isCron
     * @param bool $isUseCache
     *
     * @throws Exception
     */
    public function generateAndDeliveryFeed($feed, $forceGenerate = false, $isCron = false, $isUseCache = false)
    {
        if (!$this->isEnabled()) {
            $this->messageManager->addErrorMessage(__('Please enable Mageplaza_ProductFeed module'));

            return;
        }
        if (!$forceGenerate && !$feed->getStatus()) {
            return;
        }

        $status       = Status::ERROR;
        $delivery     = Delivery::ERROR;
        $productCount = 0;
        try {
            $productCount = $this->generateLiquidTemplate($feed, $isUseCache);
            $this->messageManager->addSuccessMessage(__('%1 feed has been generated successfully.', $feed->getName()));
            $this->feedFactory->create()->load($feed->getId())->setLastGenerated($this->date->date())->save();
            $status = Status::SUCCESS;
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (RuntimeException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('Something went wrong while generating %1 Feed. %2', $feed->getName(), $e->getMessage())
            );
        }
        if ($status === Status::SUCCESS) {
            if ($feed->getDeliveryEnable()) {
                try {
                    $this->deliveryFeed($feed);
                    $this->messageManager->addSuccessMessage(
                        __('%1 feed has been uploaded successfully', $feed->getName())
                    );
                    $delivery = Delivery::SUCCESS;
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (RuntimeException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (Exception $e) {
                    $this->messageManager->addErrorMessage(
                        __('Something went wrong while uploading %1 Feed. %2', $feed->getName(), $e->getMessage())
                    );
                }
            } else {
                $delivery = Delivery::DISABLED;
            }
        }
        $successMessage = [];
        $errorMessage   = [];

        foreach ($this->messageManager->getMessages()->getItems() as $message) {
            if (strpos($message->getText(), $feed->getName()) !== false) {
                if ($message->getType() === 'success') {
                    $successMessage[] = $message->getText();
                } else {
                    $errorMessage[] = $message->getText();
                }
            }
        }
        $successMessage = implode("\n", $successMessage);
        $errorMessage   = implode("\n", $errorMessage);

        if ($this->getEmailConfig('enabled')) {
            $generateStt = $status === Status::SUCCESS ? Events::GENERATE_SUCCESS : Events::GENERATE_ERROR;
            $generateMes = $generateStt === Events::GENERATE_SUCCESS
                ? ('<p style="color: green">' . __('%1 feed generated successful', $feed->getName()) . '</p>')
                : ('<p style="color: red">' . __('%1 feed generated fail', $feed->getName()) . '</p>');
            $deliveryStt = $delivery === Delivery::SUCCESS
                ? Events::DELIVERY_SUCCESS
                : ($delivery === Delivery::ERROR ? Events::DELIVERY_ERROR : Events::DELIVERY_DISABLED);
            $deliveryMes = $deliveryStt === Events::DELIVERY_SUCCESS
                ? '<p style="color: green">' . __('%1 feed delivery successful', $feed->getName()) . '</p>'
                : ($deliveryStt === Events::DELIVERY_ERROR
                    ? ('<p style="color: red">' . __('%1 feed delivery fail', $feed->getName()) . '</p>') : '');
            $events      = explode(',', $this->getEmailConfig('events'));
            $sendTo      = empty($this->getEmailConfig('send_to'))
                ? null : explode(',', $this->getEmailConfig('send_to'));
            if (in_array($generateStt, $events, true) || in_array($deliveryStt, $events, true)) {
                $this->sendMail(
                    $sendTo,
                    $generateMes . $deliveryMes,
                    'product_feed_email_template',
                    $feed->getStoreId()
                );
            }
        }

        $history = $this->historyFactory->create();
        $history->setData([
            'feed_id'         => $feed->getId(),
            'feed_name'       => $feed->getName(),
            'status'          => $status,
            'delivery'        => $delivery,
            'type'            => $isCron ? 'cron' : 'manual',
            'product_count'   => $productCount,
            'file'            => $feed->getFileName() . '.' . $feed->getFileType(),
            'success_message' => $successMessage,
            'error_message'   => $errorMessage
        ])->save();

        if ($isCron) {
            $this->messageManager->getMessages()->clear();
        }
    }

    /**
     * Test connection
     *
     * @param string $protocol
     * @param string $host
     * @param string $passive
     * @param string $user
     * @param string $pass
     * @param string $path
     *
     * @return int
     */
    public function testConnection($protocol, $host, $passive, $user, $pass, $path)
    {
        try {
            if ($protocol === 'sftp') {
                if (strpos($host, ':') !== false) {
                    [$host, $port] = explode(':', $host, 2);
                } else {
                    $port = Sftp::SSH2_PORT;
                }

                $connection = new libSFTP($host, $port, 10);

                if ($path) {
                    $privateKey = new RSA();
                    $privateKey->setPassword($pass);
                    $privateKey->loadKey(file_get_contents($path));
                    $pass = $privateKey;
                }

                return $connection->login($user, $pass) ? 1 : 0;
            }

            $open = $this->ftp->open([
                'host'     => $host,
                'user'     => $user,
                'password' => $pass,
                'ssl'      => true,
                'passive'  => $passive
            ]);

            return $open ? 1 : 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Delivery Feed
     *
     * @param Feed $feed
     *
     * @throws LocalizedException
     */
    public function deliveryFeed($feed)
    {
        $host           = $feed->getHostName();
        $username       = $feed->getUserName();
        $password       = $feed->getPassword();
        $privateKeyPath = $feed->getPrivateKeyPath();
        $timeout        = '20';
        $passiveMode    = $feed->getPassiveMode();
        $fileName       = $feed->getFileName() . '.' . $feed->getFileType();
        $fileUrl        = $this->getFileUrl($fileName);
        $directoryPath  = $feed->getDirectoryPath() . $fileName;

        if (!$host || !$username || !$password) {
            throw new Exception(__('Please check the Delivery information again.'));
        }

        if ($feed->getProtocol() === 'sftp') {
            if (strpos($host, ':') !== false) {
                [$host, $port] = explode(':', $host, 2);
            } else {
                $port = Sftp::SSH2_PORT;
            }
            $connection = new libSFTP($host, $port, $timeout);
            if ($privateKeyPath) {
                $privateKey = new RSA();
                $privateKey->setPassword($password);
                $privateKey->loadKey(file_get_contents(self::FEED_KEY_FILE_PATH . $privateKeyPath));
                $password = $privateKey;
            }

            if (!$connection->login($username, $password)) {
                throw new RuntimeException(__('Unable to open SFTP connection as %1@%2', $username, $host));
            }

            $content = $this->file->read($fileUrl);
            $mode    = is_readable($fileName)
                ? libSFTP::SOURCE_LOCAL_FILE : libSFTP::SOURCE_STRING;
            $connection->put($directoryPath, $content, $mode);
            $connection->disconnect();

            //2.2.x

            //            $this->sftp->open([
            //                'host' => $host,
            //                'username' => $username,
            //                'password' => $password,
            //            ]);
            //            $content = file_get_contents($fileUrl);
            //            $this->sftp->write($directoryPath, $content);
            //            $this->sftp->close();

        } else {
            $open = $this->ftp->open([
                'host'     => $host,
                'user'     => $username,
                'password' => $password,
                'ssl'      => true,
                'passive'  => $passiveMode
            ]);
            if ($open) {
                $content = $this->file->read($fileUrl);
                $this->ftp->write($directoryPath, $content);
                $this->ftp->close();
            } else {
                throw new RuntimeException(__('Unable to authenticate with server'));
            }
        }
    }

    /**
     * Prepare template
     *
     * @param Feed $feed
     * @param null|string $templateHtml
     * @param bool $saveCache
     * @param bool $isUseCache
     *
     * @return Template
     */
    public function prepareTemplate($feed, $templateHtml = null, $saveCache = false, $isUseCache = false)
    {
        $template       = new Template;
        $filtersMethods = $this->liquidFilters->getFiltersMethods();

        $template->registerFilter($this->liquidFilters);
        $templateHtml = $templateHtml ?: $this->getTemplateHtml($feed);

        if ($isUseCache) {
            $template->registerTag('for', \Mageplaza\ProductFeed\Block\Liquid\Tag\TagFor::class);
        }

        if ($saveCache) {
            $this->setFeedSessionData($feed->getId(), 'template_html', $templateHtml);
        }

        $filtersMethods[] = 'mpCorrect';

        $template->parse($templateHtml, $filtersMethods);

        return $template;
    }

    /**
     * Get Html template
     *
     * @param Feed $feed
     *
     * @return string
     */
    public function getTemplateHtml($feed)
    {
        $fileType     = $feed->getFileType();
        $templateHtml = '';

        if ($fileType === 'xml') {
            $templateHtml = $feed->getTemplateHtml();
        } else {
            $fieldSeparate = $feed->getFieldSeparate() === 'tab' ? "\t"
                : ($feed->getFieldSeparate() === 'comma' ? ',' : ';');
            $fieldAround   = $feed->getFieldAround() === 'none' ? ''
                : ($feed->getFieldAround() === 'quote' ? "'" : '"');
            $includeHeader = $feed->getIncludeHeader();
            $fieldsMap     = self::jsonDecode($feed->getFieldsMap());
            if ($fieldsMap) {
                $row = [];
                foreach ($fieldsMap as $field) {
                    $row[0][] = $field['col_name'];

                    if ($field['col_type'] === 'attribute') {
                        $row[1][] = $fieldAround . $field['col_val'] . $fieldAround;
                    } else {
                        $row[1][] = $fieldAround . $field['col_pattern_val'] . $fieldAround;
                    }
                }

                $row[0] = implode($fieldSeparate, $row[0]);
                $row[1] = implode($fieldSeparate, $row[1]);

                if ($includeHeader) {
                    $templateHtml = $row[0] . '
' . '{% for product in products %}' . $row[1] . '
{% endfor %}';
                } else {
                    $templateHtml = '{% for product in products %}' . $row[1] . '
{% endfor %}';
                }

                $templateHtml = str_replace(
                    '}}',
                    "| mpCorrect: '" . $feed->getFieldAround() . "', '" . $feed->getFieldSeparate() . "'}}",
                    $templateHtml
                );
            }
        }

        return $templateHtml;
    }

    /**
     * Generate Liquid template
     *
     * @param Feed $feed
     * @param bool $isUseCache
     *
     * @return int
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function generateLiquidTemplate($feed, $isUseCache = false)
    {
        $feedId       = $feed->getId();
        $templateHtml = $isUseCache ? $this->getFeedSessionData($feedId, 'template_html') : null;
        $template     = $this->prepareTemplate($feed, $templateHtml, false, $isUseCache);
        if ($isUseCache) {
            $prdAttr = $this->getFeedSessionData($feedId, 'product_attributes');
        } else {
            $prdAttr = [];
            $root    = $template->getRoot();
            $prdAttr = $this->getProductAttr($root->getNodelist(), $prdAttr);
        }

        $productCollectionData = $isUseCache
            ? []
            : $this->getProductsData($feed, $prdAttr);

        $reviewCollection = $this->getReviewCollection();
        $content          = $template->render([
            'products' => $productCollectionData,
            'store'    => $this->getStoreData($feed->getStoreId()),
            'reviews'  => $reviewCollection,
            'feed_id'  => $feedId
        ]);

        $this->createFeedFile($feed, $content);

        return $isUseCache ? $this->getFeedSessionData($feedId, 'product_count') : count($productCollectionData);
    }

    /**
     * Create Feed file
     *
     * @param Feed $feed
     * @param string $content
     *
     * @throws Exception
     */
    public function createFeedFile($feed, $content)
    {
        $this->file->checkAndCreateFolder(self::FEED_FILE_PATH);
        $fileName = $feed->getFileName() . '.' . $feed->getFileType();
        $fileUrl  = self::FEED_FILE_PATH . '/' . $fileName;
        $this->file->write($fileUrl, $content);
    }

    /**
     * Get product attribute
     *
     * @param array $nodeList
     * @param array $prdAttr
     *
     * @return array
     */
    public function getProductAttr($nodeList, $prdAttr)
    {
        /** @var Variable|TagFor $node */
        foreach ($nodeList as $node) {
            if (!is_object($node)) {
                continue;
            }
            if ($node instanceof Variable && strncmp($node->getName(), 'product.', 8) === 0) {
                $prdAttr[] = str_replace('product.', '', $node->getName());
            } elseif (method_exists($node, 'getNodelist')) {
                $prdAttr = array_merge($this->getProductAttr($node->getNodelist(), $prdAttr), $prdAttr);
            }
        }

        return $prdAttr;
    }

    /**
     * Get data store
     *
     * @param string|int $id
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getStoreData($id)
    {
        $store     = $this->storeManager->getStore($id);
        $locale    = $this->resolver->getLocale();
        $storeData = [
            'locale_code' => $locale,
            'base_url'    => $store->getBaseUrl()
        ];

        return $storeData;
    }

    /**
     * Get review collection
     *
     * @return AbstractCollection
     */
    public function getReviewCollection()
    {
        $collection = $this->reviewFactory->create()->getCollection();
        /** @var $review Review */
        foreach ($collection as $review) {
            $review->setUrl($review->getReviewUrl());
            $product = $this->productFactory->create()->load($review->getEntityPkValue());
            $product->setUrl($product->getProductUrl());
            $rating = $this->reviewSummaryFactory->create()->load($review->getId())->getRatingSummary() * 5 / 100;
            $review->setRating($rating);
            $review->setProduct($product);
        }

        return $collection;
    }

    /**
     * Get products data
     *
     * @param Feed $feed
     * @param array $productAttributes
     * @param array $productIds
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getProductsData($feed, $productAttributes = [], $productIds = [])
    {
        $campaignUrl = '';
        $campaignUrl .= $feed->getCampaignSource() ? '?utm_source=' . $feed->getCampaignSource() : '';
        $campaignUrl .= $feed->getCampaignMedium() ? '&utm_medium=' . $feed->getCampaignMedium() : '';
        $campaignUrl .= $feed->getCampaignName() ? '&utm_campaign=' . $feed->getCampaignName() : '';
        $campaignUrl .= $feed->getCampaignTerm() ? '&utm_term=' . $feed->getCampaignTerm() : '';
        $campaignUrl .= $feed->getCampaignContent() ? '&utm_content=' . $feed->getCampaignContent() : '';

        $categoryMap = $this->unserialize($feed->getCategoryMap());

        $allCategory = $this->categoryCollectionFactory->create();
        $allCategory->setStoreId($feed->getStoreId())->addAttributeToSelect('name');
        $categoriesName = [];
        /** @var $item Category */
        foreach ($allCategory as $item) {
            $categoriesName[$item->getId()] = $item->getName();
        }

        $allSelectProductAttributes = $this->prdAttrCollectionFactory->create()
            ->addFieldToFilter('frontend_input', ['in' => ['multiselect', 'select']])
            ->getColumnValues('attribute_code');

        $matchingProductIds = !empty($productIds) ? $productIds : $feed->getMatchingProductIds();
        $productCollection  = $this->productFactory->create()->getCollection()
            ->addAttributeToSelect($productAttributes)->addStoreFilter($feed->getStoreId())
            ->addFieldToFilter('entity_id', ['in' => $matchingProductIds])->addMediaGalleryData();

        $result = [];
        /** @var $product Product */
        foreach ($productCollection as $product) {
            $typeInstance           = $product->getTypeInstance();
            $childProductCollection = $typeInstance->getAssociatedProducts($product);
            if ($childProductCollection) {
                $associatedData = [];
                foreach ($childProductCollection as $item) {
                    $associatedData = $item->getData();
                }
                $product->setAssociatedProducts($associatedData);
            } else {
                $product->setAssociatedProducts([]);
            }

            $stockItem       = $this->stockState->getStockItem(
                $product->getId(),
                $feed->getStoreId()
            );
            $qty             = $stockItem->getQty();
            $categories      = $product->getCategoryCollection()->addAttributeToSelect('*');
            $relatedProducts = [];
            foreach ($product->getRelatedProducts() as $item) {
                $relatedProducts[] = $item->getData();
            }
            $crossSellProducts = [];
            foreach ($product->getCrossSellProducts() as $item) {
                $crossSellProducts[] = $item->getData();
            }
            $upSellProducts = [];
            foreach ($product->getUpSellProducts() as $item) {
                $upSellProducts[] = $item->getData();
            }
            $oriProduct = $this->productFactory->create()->load($product->getId());
            $finalPrice = $this->convertPrice($oriProduct->getFinalPrice(), $feed->getStoreId());
            $storeId    = $feed->getStoreId() ?: $this->storeManager->getDefaultStoreView()->getId();
            $product->setStoreId($storeId);
            $productLink = $this->getProductUrl($oriProduct, $storeId) . $campaignUrl;
            $imageLink   = $oriProduct->getImage() ? $this->storeManager->getStore($feed->getStoreId())
                    ->getBaseUrl(UrlAbstract::URL_TYPE_MEDIA)
                . 'catalog/product' . $oriProduct->getImage() : '';
            $images      = $product->getMediaGalleryImages()->getSize() ? $product->getMediaGalleryImages() : [[]];
            if (is_object($images)) {
                $imagesData = [];
                foreach ($images->getItems() as $item) {
                    $imagesData[] = $item->getData();
                }
                $images = $imagesData;
            }
            /** @var $category Category */
            $lv             = 0;
            $categoryPath   = '';
            $cat            = new DataObject();
            $categoriesData = [];
            foreach ($categories as $category) {
                if ($lv < $category->getLevel()) {
                    $lv  = $category->getLevel();
                    $cat = $category;
                }
                $categoriesData[] = $category->getData();
            }
            $mapping = '';
            if (isset($categoryMap[$cat->getId()])) {
                $mapping = $categoryMap[$cat->getId()];
            }
            $catPaths = array_reverse(explode(',', $cat->getPathInStore()));
            foreach ($catPaths as $index => $catId) {
                if ($index === (count($catPaths) - 1)) {
                    $categoryPath .= isset($categoriesName[$catId]) ? $categoriesName[$catId] : '';
                } else {
                    $categoryPath .= (isset($categoriesName[$catId]) ? $categoriesName[$catId] : '') . ' > ';
                }
            }

            $oriProduct->isAvailable() ? $product->setData('quantity_and_stock_status', 'in stock')
                : $product->setData('quantity_and_stock_status', 'out of stock');

            $noneAttr = [
                'categoryCollection',
                'relatedProducts',
                'crossSellProducts',
                'upSellProducts',
                'final_price',
                'link',
                'image_link',
                'images',
                'category_path',
                'mapping',
                'qty',
            ];

            // Convert attribute value to attribute text
            foreach ($productAttributes as $attributeCode) {
                try {
                    if ($attributeCode === 'quantity_and_stock_status'
                        || in_array($attributeCode, $noneAttr, true)
                        || !in_array($attributeCode, $allSelectProductAttributes, true)
                        || !$product->getData($attributeCode)
                    ) {
                        continue;
                    }
                    $attributeText = $product->getResource()->getAttribute($attributeCode)
                        ->setStoreId($feed->getStoreId())->getFrontend()->getValue($product);
                    if (is_array($attributeText)) {
                        $attributeText = implode(',', $attributeText);
                    }
                    if ($attributeText) {
                        $product->setData($attributeCode, $attributeText);
                    }
                } catch (Exception $e) {
                    continue;
                }
            }

            $product->setData('categoryCollection', $categoriesData);
            $product->setData('relatedProducts', $relatedProducts);
            $product->setData('crossSellProducts', $crossSellProducts);
            $product->setData('upSellProducts', $upSellProducts);
            $product->setData('final_price', $finalPrice);
            $product->setData('link', $productLink);
            $product->setData('image_link', $imageLink);
            $product->setData('images', $images);
            $product->setData('category_path', $categoryPath);
            $product->setData('mapping', $mapping);
            $product->setData('qty', $qty);
            $result[] = self::jsonDecode(self::jsonEncode($product->getData()));

            if ($this->useGoogleShoppingApi($feed)) {
                $this->syncProductToGoogleShopping($feed, $product);
            }
        }

        return $result;
    }

    /**
     * @param Feed $feed
     *
     * @return bool
     */
    public function useGoogleShoppingApi($feed)
    {
        return $feed->getFileType() === 'xml'
            && $this->getConfigGeneral('google_shopping/enabled')
            && $this->getConfigGeneral('google_shopping/merchant_id')
            && $this->getConfigGeneral('google_shopping/client_id')
            && $this->getConfigGeneral('google_shopping/client_secret');
    }

    /**
     * Convert price
     *
     * @param int $amount
     * @param int $storeId
     *
     * @return float|int
     * @throws NoSuchEntityException
     */
    public function convertPrice($amount = 0, $storeId = null)
    {
        if ($storeId === null) {
            $storeId = $this->storeManager->getStore()->getStoreId();
        }

        return (float) $this->priceCurrency->convert($amount, $storeId);
    }

    /**
     * Get url file
     *
     * @param string $filename
     *
     * @return string
     */
    public function getFileUrl($filename)
    {
        return $this->_urlBuilder->getBaseUrl([
                '_type' => UrlAbstract::URL_TYPE_MEDIA
            ]) . 'mageplaza/feed/' . $filename;
    }

    /**
     * Prepare generate
     *
     * @param Feed $feed
     *
     * @return array
     * @throws Exception
     */
    public function prepareGenerate($feed)
    {
        $feedId = $feed->getId();
        $this->resetFeedSessionData($feedId);
        $template = $this->prepareTemplate($feed, null, true);
        $root     = $template->getRoot();
        $prdAttr  = [];
        $prdAttr  = $this->getProductAttr($root->getNodelist(), $prdAttr);
        $this->setFeedSessionData($feedId, 'product_attributes', $prdAttr);
        $productIds = $feed->getMatchingProductIds();
        $chunk      = array_chunk($productIds, 1000);
        $this->setFeedSessionData($feedId, 'product_chunk', $chunk);
        try {
            $this->driverFile->deleteDirectory(self::FEED_FILE_PATH . 'collection/' . $feedId . '/');
        } catch (Exception $e) {
        }

        return [
            'product_count' => count($productIds)
        ];
    }

    /**
     * Prepare data of product
     *
     * @param Feed $feed
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function prepareProductData($feed)
    {
        $feedId       = $feed->getId();
        $productAttr  = $this->getFeedSessionData($feedId, 'product_attributes');
        $productChunk = $this->getFeedSessionData($feedId, 'product_chunk');
        $productCount = (int) $this->getFeedSessionData($feedId, 'product_count');
        $ids          = array_shift($productChunk);
        $collection   = $this->getProductsData($feed, $productAttr, $ids);
        $productCount += count($collection);
        $name         = $ids ? current($ids) . end($ids) : '0';
        $this->createFeedCollectionFile($feedId, self::jsonEncode($collection), $name);
        $this->setFeedSessionData($feedId, 'product_chunk', $productChunk);
        $this->setFeedSessionData($feedId, 'product_count', $productCount);

        return [
            'complete'      => empty($productChunk),
            'product_count' => $productCount
        ];
    }

    /**
     * Get Feed session data
     *
     * @param string|int $feedId
     * @param string $path
     *
     * @return mixed|null
     */
    public function getFeedSessionData($feedId, $path)
    {
        $data = $this->session->getData("mp_product_feed_data_{$feedId}");

        return $data[$path] ?? null;
    }

    /**
     * Set Feed session data
     *
     * @param string|int $feedId
     * @param string $path
     * @param mixed $value
     */
    public function setFeedSessionData($feedId, $path, $value)
    {
        $data        = $this->session->getData("mp_product_feed_data_{$feedId}");
        $data[$path] = $value;
        $this->session->setData("mp_product_feed_data_{$feedId}", $data);
    }

    /**
     * Reset Feed session data
     *
     * @param string|int $feedId
     */
    public function resetFeedSessionData($feedId)
    {
        $this->session->setData("mp_product_feed_data_{$feedId}", null);
    }

    /**
     * Process request
     *
     * @param Feed $feed
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function processRequest($feed)
    {
        $step = $this->_getRequest()->getParam('step');
        if ($step === 'prepare_generate') {
            return $this->prepareGenerate($feed);
        }

        if ($step === 'prepare_product_data') {
            return $this->prepareProductData($feed);
        }

        if ($step === 'render') {
            $this->generateAndDeliveryFeed($feed, true, false, true);
            $this->messageManager->getMessages(true);
            $this->driverFile->deleteDirectory(self::FEED_FILE_PATH . 'collection/' . $feed->getId() . '/');

            return [
                'complete' => true,
            ];
        }

        return [
            'error'   => true,
            'message' => __('Something went wrong while generating feed')
        ];
    }

    /**
     * Create file of Feed collection
     *
     * @param int|string $feedId
     * @param string $content
     * @param string $name
     *
     * @throws Exception
     */
    public function createFeedCollectionFile($feedId, $content, $name)
    {
        $this->file->checkAndCreateFolder(self::FEED_FILE_PATH . 'collection/' . $feedId);
        $fileUrl = self::FEED_FILE_PATH . 'collection/' . $feedId . '/' . $name;
        $this->file->write($fileUrl, $content);
    }

    /**
     * Get paths of Feed collection
     *
     * @param string|int $feedId
     *
     * @return string[]
     * @throws FileSystemException
     */
    public function getFeedCollectionPaths($feedId)
    {
        $directoryUrl = self::FEED_FILE_PATH . 'collection/' . $feedId . '/';
        $paths        = $this->driverFile->readDirectory($directoryUrl);

        usort($paths, function ($pathA, $pathB) {
            $pathArrayA = explode('/', $pathA);
            $valueA     = end($pathArrayA);

            $pathArrayB = explode('/', $pathB);
            $valueB     = end($pathArrayB);

            return $valueB < $valueA;
        });

        return $paths;
    }

    /**
     * Read file
     *
     * @param string $path
     *
     * @return bool|string
     */
    public function readFile($path)
    {
        return $this->file->read($path);
    }

    /**
     * Get product view url
     *
     * @param Product $product
     * @param int $storeId
     *
     * @return string
     */
    public function getProductUrl($product, $storeId)
    {
        if ($storeId) {
            $this->urlModel->setScope($storeId);
        }

        $productLink            = '';
        $routeParams['id']      = $product->getId();
        $routeParams['s']       = $product->getUrlKey();
        $routeParams['_nosid']  = true;
        $routeParams['_escape'] = true;
        $categoryId             = null;

        if ($product->getCategoryId() && !$product->getDoNotUseCategoryId()) {
            $categoryId = $product->getCategoryId();
        }
        if ($categoryId) {
            $routeParams['category'] = $categoryId;
        }

        $requestPath = $product->getRequestPath();
        if (empty($requestPath) && $requestPath !== false) {
            $filterData = [
                UrlRewrite::ENTITY_ID   => $product->getId(),
                UrlRewrite::ENTITY_TYPE => ProductUrlRewriteGenerator::ENTITY_TYPE,
                UrlRewrite::STORE_ID    => $storeId,
            ];

            $rewrite = $this->urlFinder->findOneByData($filterData);

            if ($rewrite) {
                $productLink = $product->getUrlModel()->getUrlInStore($product, $routeParams);
            } else {
                $productLink = $this->urlModel->getUrl('catalog/product/view', $routeParams);
            }
        }

        return $productLink;
    }

    /**
     * @return string
     */
    public function getMerchantId()
    {
        $merchantId = $this->getConfigGeneral('google_shopping/merchant_id');

        return $this->decrypt($merchantId);
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        $clientId = $this->getConfigGeneral('google_shopping/client_id');

        return $this->decrypt($clientId);
    }

    /**
     * @return string
     */
    public function getClientSecret()
    {
        $clientSecret = $this->getConfigGeneral('google_shopping/client_secret');

        return $this->decrypt($clientSecret);
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getRedirectURIs()
    {
        $redirectUris = $this->getConfigGeneral('google_shopping/redirect_URIs');

        return $redirectUris ? : $this->getAuthorizedRedirectURIs();
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function decrypt($value)
    {
        return $this->encryptor->decrypt($value);
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getAuthorizedRedirectURIs()
    {
        $storeId = $this->getScopeUrl();
        /** @var Store $store */
        $store = $this->storeManager->getStore($storeId);

        return $this->_getUrl('mpproductfeed/index/token', [
            '_nosid'  => true,
            '_scope'  => $storeId,
            '_secure' => $store->isUrlSecure()
        ]);
    }

    /**
     * @return int
     * @throws LocalizedException
     */
    protected function getScopeUrl()
    {
        $scope = $this->_request->getParam(ScopeInterface::SCOPE_STORE) ?: $this->storeManager->getStore()->getId();

        if ($website = $this->_request->getParam(ScopeInterface::SCOPE_WEBSITE)) {
            $scope = $this->storeManager->getWebsite($website)->getDefaultStore()->getId();
        }

        return $scope;
    }

    /**
     * @param string $url
     * @param string $method
     * @param string $params
     *
     * @return mixed|string
     */
    public function requestData($url, $method = Zend_Http_Client::POST, $params = '', $isAccessToken = false)
    {
        $httpAdapter = $this->curlFactory->create();
        if ($isAccessToken) {
            $headers = [
                'Host: oauth2.googleapis.com',
                'Content-Type: application/x-www-form-urlencoded'
            ];
        } else {
            $headers = [
                'Content-Type:application/json',
                'Authorization: Bearer ' . $this->getAccessToken()
            ];
        }

        if (($method === Zend_Http_Client::POST && !$isAccessToken)) {
            $params = self::jsonEncode($params);
        }

        $httpAdapter->write($method, $url, '1.1', $headers, $params);
        $result   = $httpAdapter->read();
        $response = Zend_Http_Response::extractBody($result);
        $response = self::jsonDecode($response);
        $httpAdapter->close();

        return $response;
    }

    /**
     * @param array $resp
     * @param bool $isRefreshToken
     */
    public function saveAPIData($resp, $isRefreshToken = false)
    {
        if ($isRefreshToken) {
            $resp['refresh_token'] = $this->getRefreshToken();
        }

        $accessToken = $this->encryptor->encrypt($resp['access_token']);
        $accessData  = $this->encryptor->encrypt(self::jsonEncode($resp));
        $this->saveConfig($this->getMpProductFeedShoppingPathByKey('access_token'), $accessToken);
        $this->saveConfig($this->getMpProductFeedShoppingPathByKey('access_data'), $accessData);
        $this->saveConfig($this->getMpProductFeedShoppingPathByKey('last_request_token'), time());
    }

    /**
     * @param string $path
     * @param string $value
     */
    public function saveConfig($path, $value)
    {
        $this->modelConfig->saveConfig($path, $value);
    }

    /**
     * @param string $field
     *
     * @return string
     */
    public function getMpProductFeedShoppingPathByKey($field)
    {
        return self::CONFIG_MODULE_PATH . '/general/google_shopping/' . $field;
    }

    /**
     * @return mixed|string
     */
    public function getRefreshToken()
    {
        $accessData = $this->getAccessData();
        if (isset($accessData['refresh_token'])) {
            return $accessData['refresh_token'];
        }

        return '';
    }

    /**
     * @return array|mixed
     */
    public function getAccessData()
    {
        $accessData = $this->getConfigGeneral('google_shopping/access_data');

        if ($accessData) {
            return self::jsonDecode($this->decrypt($accessData));
        }

        return [];
    }

    /**
     * @param Feed $feed
     * @param Product $product
     *
     * @throws NoSuchEntityException
     */
    public function syncProductToGoogleShopping(Feed $feed, Product $product)
    {
        $mapping = self::jsonDecode($feed->getMapping());
        $record  = [];
        foreach ($mapping as $field => $mappingField) {
            $record[$field] = $this->processMappingField($mappingField, $product);
        }
        $record['offerId']              = $record['id'];
        $record['price']                = [
            'value'    => $record['price'],
            'currency' => $this->storeManager->getStore($feed->getStoreId())->getCurrentCurrencyCode()
        ];
        $promotionIds                   = $record['promotionIds'];
        $productTypes                   = $record['productTypes'];
        $record['promotionIds']         = [];
        $record['productTypes']         = [];
        $record['additionalImageLinks'] = [];
        $record['promotionIds'][]       = $promotionIds;
        $record['productTypes'][]       = $productTypes;
        foreach ($product->getData('images') as $image) {
            $record['additionalImageLinks'][] = $image['url'];
        }

        if (!$record['identifierExists']) {
            $record['identifierExists'] = false;
        }

        $merchantId = $this->getMerchantId();
        $url        = self::GOOGLE_SHOPPING_URL . $merchantId . '/products';
        try {
            $this->sendRequest($url, 'POST', $record, false);
        } catch (Exception $e) {
            $this->_logger->critical($e->getMessage());
        }
    }

    /**
     * @param string $url
     * @param string $method
     * @param string $params
     * @param bool $isAccessToken
     *
     * @return mixed
     * @throws Exception
     */
    public function sendRequest($url, $method, $params = '', $isAccessToken = false)
    {
        $this->checkRefreshAccessToken();

        return $this->requestData($url, $method, $params, $isAccessToken);
    }

    /**
     * Check and refresh access token after one hour
     *
     * @throws Exception
     */
    public function checkRefreshAccessToken()
    {
        $lastRequestToken = (int) $this->getLastRequestToken();

        if ($lastRequestToken + 3600 < time()) {
            $refreshData = [
                'refresh_token' => $this->getRefreshToken(),
                'client_id'     => $this->getClientId(),
                'client_secret' => $this->getClientSecret(),
                'grant_type'    => 'refresh_token'
            ];

            $resp = $this->requestData(
                self::GOOGLE_SHOPPING_TOKEN,
                Zend_Http_Client::POST,
                http_build_query($refreshData),
                true
            );

            if (isset($resp['access_token'])) {
                $this->saveAPIData($resp, true);
            } else {
                throw new LocalizedException(__('Cannot refresh access token.'));
            }
        }
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        $config = $this->configCollectionFactory->create()
            ->addFieldToFilter('path', $this->getMpProductFeedShoppingPathByKey('access_token'))
            ->getFirstItem();
        if ($config->getValue()) {
            return $this->decrypt($config->getValue());
        }

        return '';
    }

    /**
     * Load config without cache
     *
     * @return string
     */
    public function getLastRequestToken()
    {
        $config = $this->configCollectionFactory->create()
            ->addFieldToFilter('path', $this->getMpProductFeedShoppingPathByKey('last_request_token'))
            ->getFirstItem();
        if ($config->getId()) {
            return $config->getValue();
        }

        return '';
    }

    /**
     * @param array $mappingField
     * @param Product $product
     *
     * @return string
     */
    public function processMappingField($mappingField, $product)
    {
        $value = $mappingField['value'];
        if ($mappingField['value']) {
            $data       = $this->helperMapping->matchData($mappingField['value']);
            $dataFields = [];
            foreach ($data as $field) {
                if (!isset($dataFields[$field])) {
                    $currentValue = $this->processProductField($field, $product);

                    if ($currentValue && !is_array($currentValue)) {
                        $value = $this->replaceValue($field, $currentValue, $value);
                    }

                    $dataFields[$field] = $currentValue;
                }
            }

            if (!$value) {
                $value = $mappingField['default'];
            }

            return $this->formatValue($value, $mappingField['type']);
        }

        return $mappingField['default'];
    }

    /**
     * @param string $field
     * @param Product $product
     *
     * @return mixed
     */
    public function processProductField($field, $product)
    {
        if ($field === 'description') {
            return htmlspecialchars($product->getData($field));
        }

        if ($field === 'quantity_and_stock_status') {
            return $product->getData($field);
        }

        if ($product->getResource()) {
            if ($productAttribute = $product->getResource()->getAttribute($field)) {
                return $productAttribute->getFrontend()->getValue($product);
            }
        }

        return $product->getData($field);
    }

    /**
     * @param string $search
     * @param string $replace
     * @param string $value
     *
     * @return mixed
     */
    public function replaceValue($search, $replace, $value)
    {
        return str_replace('{{' . $search . '}}', $replace, $value);
    }

    /**
     * @param mixed $value
     * @param string $type
     *
     * @return string
     */
    public function formatValue($value, $type)
    {
        if ($value) {
            /** Replace all option match in {{}} */
            $value = preg_replace(Mapping::PATTERN_OPTIONS, '', $value);
        }

        switch ($type) {
            case 'int':
                $value = (int) $value;
                break;
            case 'float':
                $value = (float) $value;
                break;
            case 'boolean':
                $value = (bool) $value;
                break;
            case 'string':
                $value = (string) $value;
                break;
        }

        return $value;
    }
}
