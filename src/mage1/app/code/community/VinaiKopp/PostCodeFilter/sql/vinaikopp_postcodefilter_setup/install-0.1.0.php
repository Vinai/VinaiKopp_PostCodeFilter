<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$installer->startSetup();

$tableName = $installer->getTable('vinaikopp_postcodefilter/rule');
if ($installer->getConnection()->isTableExists($tableName)) {
    $installer->getConnection()->dropTable($tableName);
}
$primaryKeys = ['country', 'customer_group_id'];
$table = $installer->getConnection()->newTable($tableName)
    ->addColumn('country', Varien_Db_Ddl_Table::TYPE_CHAR, 2)
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_INTEGER)
    ->addColumn('post_codes', Varien_Db_Ddl_Table::TYPE_TEXT)
    ->addIndex(
        $installer->getIdxName($tableName, $primaryKeys, Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY),
        $primaryKeys,
        Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY
    );
$installer->getConnection()->createTable($table);

$installer->endSetup();
