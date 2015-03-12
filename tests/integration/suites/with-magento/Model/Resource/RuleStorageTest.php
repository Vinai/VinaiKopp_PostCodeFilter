<?php

namespace VinaiKopp\PostCodeFilter;

/**
 * @covers \VinaiKopp_PostCodeFilter_Model_Resource_RuleStorage
 */
class RuleStorageTest extends IntegrationTestCase
{
    /**
     * @var \VinaiKopp_PostCodeFilter_Model_Resource_RuleStorage
     */
    private $storage;

    /**
     * @var \Varien_Db_Adapter_Interface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockReadConnection;

    /**
     * @var \Varien_Db_Adapter_Interface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockWriteConnection;

    protected function setUp()
    {
        $this->mockReadConnection = $this->getMock(\Varien_Db_Adapter_Interface::class);
        $this->mockReadConnection->expects($this->any())->method('select')->willReturnCallback(function () {
            $mock = $this->getMock(\Varien_Db_Select::class, [], [], '', false);
            $mock->expects($this->any())->method('from')->willReturnSelf();
            $mock->expects($this->any())->method('where')->willReturnSelf();
            return $mock;
        });
        $this->mockWriteConnection = $this->getMock(\Varien_Db_Adapter_Interface::class);
        $this->storage = new \VinaiKopp_PostCodeFilter_Model_Resource_RuleStorage(
            $this->mockReadConnection,
            $this->mockWriteConnection
        );
    }

    /**
     * @test
     */
    public function itShouldImplementRuleStorage()
    {
        $this->assertInstanceOf(RuleStorage::class, $this->storage);
    }

    /**
     * @test
     */
    public function itShouldReturnAnArrayOfAllRules()
    {
        $this->mockReadConnection->expects($this->once())->method('fetchAll')
            ->willReturn([
                ['country' => 'DE', 'customer_group_id' => 2, 'post_codes' => '1,2,3,4'],
                ['country' => 'DE', 'customer_group_id' => 3, 'post_codes' => '1,2,3,4'],
                ['country' => 'AT', 'customer_group_id' => 2, 'post_codes' => '55,66'],
            ]);
        $result = $this->storage->findAllRules();
        $this->assertInternalType('array', $result);
        $this->assertCount(3, $result);
        foreach ($result as $record) {
            $this->assertInternalType('array', $record['post_codes']);
        }
    }

    /**
     * @test
     */
    public function itShouldReturnAnEmptyArrayIfNoMatchIsFound()
    {
        $this->mockReadConnection->expects($this->once())->method('fetchOne')
            ->willReturn([]);
        $this->assertSame([], $this->storage->findPostCodesByCountryAndGroupId('DE', 3));
    }

    /**
     * @test
     */
    public function itShouldReturnAnArrayIfAMatchIsFound()
    {
        $this->mockReadConnection->expects($this->once())->method('fetchOne')->willReturn([
            'country' => 'DE',
            'customer_group_id' => 3,
            'post_codes' => '1,2,3,4'
        ]);
        $result = $this->storage->findPostCodesByCountryAndGroupId('DE', 3);
        $this->assertEquals([
            'country' => 'DE',
            'customer_group_id' => 3,
            'post_codes' => ['1', '2', '3', '4']
        ], $result);
    }

    /**
     * @test
     */
    public function itShouldCreateANewRule()
    {
        $this->mockWriteConnection->expects($this->once())->method('insert');
        $ruleToAdd = new RuleToAdd(
            CustomerGroupId::fromInt(5),
            Country::fromCode('NZ'),
            PostCodeList::fromArray(['1234', '5678'])
        );
        $this->storage->create($ruleToAdd);
    }
    
    /**
     * @test
     */
    public function itShouldDeleteAnExistingRule()
    {
        $this->mockWriteConnection->expects($this->once())->method('delete')
            ->with(
                $this->isType('string'),
                [
                    'country=?' => 'GB',
                    'customer_group_id=?' => 10
                ]
            );
        $ruleToDelete = new RuleToDelete(
            CustomerGroupId::fromInt(10),
            Country::fromCode('GB')
        );
        $this->storage->delete($ruleToDelete);
    }
}
