<?php

namespace AHT\UiComponent\Controller\Adminhtml\Attrcustomer;

use Exception;
use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Backend\App\Action;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResource;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\Customer;

/**
 * Product attribute save controller.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends Action
{
    protected $attributeResource;

    protected $eavSetupFactory;

    protected $eavConfig;

    protected $moduleDataSetup;

    protected $customerResource;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        Config $eavConfig,
        Action\Context $context,
        CustomerResource $customerResource
    ) {

        $this->eavSetupFactory = $eavSetupFactory;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavConfig = $eavConfig;
        $this->customerResource = $customerResource;
        parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $attributeCode = $data['attribute_code'];
        $source = '';
        // echo "<pre>";
        // var_dump($data);
        // die;
        if ($data['frontend_input'] == 'boolean' || $data['frontend_input'] == 'select') {
            $source = 'Magento\Eav\Model\Entity\Attribute\Source\Boolean';
        }

        if($data['position'] < 200){
            $data['position'] = 300;
        }
        
        if ($data) {
            try {
                $this->moduleDataSetup->getConnection()->startSetup();
                /** @var EavSetup $eavSetup */
                $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

                $eavSetup->addAttribute(Customer::ENTITY, $attributeCode, [
                    'label' => $data['frontend_label'],
                    'type' => $data['backend_type'],
                    'input' => $data['frontend_input'],
                    'required' => $data['is_required'],
                    'visible' => $data['is_visible'],
                    'system' => $data['is_system'],
                    'position' => $data['position'],
                    'source' => $source
                ]);

                $customerAttribute = $this->eavConfig->getAttribute(Customer::ENTITY, $attributeCode);
                $customerAttribute->setData(['used_in_forms', array_values($data['used_in_forms'])]);
                $this->customerResource->save($customerAttribute);
                $this->moduleDataSetup->getConnection()->endSetup();
            }catch (\Exception $e) {
                $this->messageManager->addSuccessMessage(__('Added attribute' ) . $data['frontend_label'] . __(' susccessfully!'));
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
