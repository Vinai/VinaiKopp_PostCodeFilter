<?php

namespace VinaiKopp\PostCodeFilter;

/**
 * @coversNothing
 */
class DbTest extends Mage1IntegrationTestCase
{
    private $tableName;

    protected function setUp()
    {
        $this->tableName = \Mage::getSingleton('core/resource')->getTableName('vinaikopp_postcodefilter/rule');
        if (! $this->tableName) {
            $this->markTestSkipped('No table name registered');
        }
    }

    /**
     * @test
     */
    public function itShouldCreateATable()
    {
        $this->assertTableExists($this->tableName);
    }

    private function assertTableExists($tableName)
    {
        /** @var \Varien_Db_Adapter_Interface $db */
        $db = \Mage::getSingleton('core/resource')->getConnection('default_read');
        $this->assertTrue($db->isTableExists($tableName), sprintf('Table "%s" does not exist', $tableName));
    }
}
