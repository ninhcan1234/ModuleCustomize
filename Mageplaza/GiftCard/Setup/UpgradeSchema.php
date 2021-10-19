<?php

namespace Mageplaza\GiftCard\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {

        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '5.0.1', '<')) {
            if ($installer->tableExists('giftcard_history')) {
                if ($installer->tableExists('giftcard_code')) {
                    if ($installer->tableExists('customer_entity')) {
                        $connection = $installer->getConnection();
                        $connection->addForeignKey(
                            $installer->getFkName('giftcard_history', 'giftcard_id', 'giftcard_code', 'giftcard_id'),
                            'giftcard_history',
                            'giftcard_id',
                            'giftcard_code',
                            'giftcard_id',
                            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                        );

                        $connection->addForeignKey(
                            $installer->getFkName('giftcard_history', 'customer_id', 'customer_entity', 'entity_id'),
                            'giftcard_history',
                            'customer_id',
                            'customer_entity',
                            'entity_id',
                            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                        );
                    }
                }
            }
        }

        $installer->endSetup();
    }
}
