<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace AHT\UiComponent\Controller\Adminhtml\Attrcustomer;

use Exception;
use \Magento\Backend\App\Action;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Customer\Model\Customer;
class Delete extends Action
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    protected $_attributeResourceModel;
    protected $_attributeFactory;
    protected $moduleDataSetup;

    public function __construct(
        Action\Context $context,
        \Magento\Customer\Model\ResourceModel\Attribute $attributeResourceModel,
        \Magento\Customer\Model\AttributeFactory $attributeFactory,
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->_attributeResourceModel = $attributeResourceModel;
        $this->_attributeFactory = $attributeFactory;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->moduleDataSetup = $moduleDataSetup;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $attributeModel = $this->_attributeFactory->create();
        $id = $this->getRequest()->getParam('id');

        if ($id) {
            $this->_attributeResourceModel->load($attributeModel, $id);
            $attributeCode = $attributeModel['attribute_code'];
            try{
                $this->moduleDataSetup->getConnection()->startSetup();
                /** @var EavSetup $eavSetup */
                $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
                $this->messageManager->addSuccessMessage(__('You deleted successfully the '. $attributeCode));
                $eavSetup->removeAttribute(Customer::ENTITY, $attributeCode );
                $this->moduleDataSetup->getConnection()->endSetup();
            }catch(Exception $e){
                $this->messageManager->addErrorMessage(__('Has some error while saving...!'));
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
