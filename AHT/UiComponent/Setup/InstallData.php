<?php
namespace AHT\UiComponent\Setup;

use Magento\Customer\Model\ResourceModel\Attribute;
use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{

    private $attributeResource;

    private $eavSetupFactory;

    private $eavConfig;

    public function __construct(
        Attribute $attributeResource,
        EavSetupFactory $eavSetupFactory,
        Config $eavConfig
    ) {

        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->attributeResource = $attributeResource;
    }


    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup ->startSetup();

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $attributes = [
            'organization_name' =>[
                'type' => 'text',
                'label' => 'Organization name',
                'input' => 'text',
                'required' => false,
                'visible' => true,
                'position' =>999,
                'system' => 0
            ],
            'contact_phone_number'=>[
                'type' => 'text',
                'label' => 'Contact phone number',
                'input' => 'text',
                'required' => true,
                'visible' => true,
                'position' =>1000,
                'system' => 0
            ],
            'company_type'=>[
                'type' => 'int',
                'label' => 'Company type',
                'input' => 'select',
                'source'=>'AHT\UiComponent\Model\Config\Source\CompanyTypeSelect',
                'required' => true,
                'visible' => true,
                'position' =>1001,
                'system' => 0
            ],
            'is_sale_agent'=>[
                'type' => 'int',
                'label' => 'Is Sale Agent',
                'input' => 'boolean',
                'source'=> 'AHT\SaleAgent\Model\Attribute\Source\IsAgent',
                'required' => true,
                'visible' => true,
                'position' =>1002,
                'system' => 0
            ]
        ];

        foreach($attributes as $attribute => $value){
            $eavSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, $attribute);
            $eavSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, $attribute, $value);
            $customerAttribute= $this->eavConfig->getAttribute(\Magento\Customer\Model\Customer::ENTITY, $attribute);
            $customerAttribute->setData(
                'used_in_forms',
                [
                    'adminhtml_customer',
                    'adminhtml_checkout',
                    'customer_account_create',
                    'customer_account_edit',
                ]
            );
            $this->attributeResource->save($customerAttribute);
        }

        $setup->endSetup();
    }
}