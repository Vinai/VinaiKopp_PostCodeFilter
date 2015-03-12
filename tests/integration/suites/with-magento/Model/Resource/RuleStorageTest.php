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
        $this->addMockSelectFactory($this->mockReadConnection);
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
        $this->mockReadConnection->expects($this->once())->method('fetchOne')->willReturn('1,2,3,4');
        $this->assertEquals(['1', '2', '3', '4'], $this->storage->findPostCodesByCountryAndGroupId('DE', 3));
    }

    /**
     * @test
     */
    public function itShouldReturnAnArrayForTheGivenCountryAndCustomerGroupIds()
    {
        $this->mockReadConnection->expects($this->once())->method('fetchAll')
            ->willReturn([
                ['country' => 'DE', 'customer_group_id' => 2, 'post_codes' => '1,2,3,4'],
                ['country' => 'DE', 'customer_group_id' => 3, 'post_codes' => '1,2,3,4'],
            ]);
        $expected = [
            ['country' => 'DE', 'customer_group_id' => 2, 'post_codes' => ['1', '2', '3', '4']],
            ['country' => 'DE', 'customer_group_id' => 3, 'post_codes' => ['1', '2', '3', '4']],
        ];
        $result = $this->storage->findRulesByCountryAndGroupIds('DE', [2, 3]);
        $this->assertSame($expected, $result);
    }

    /**
     * @test
     */
    public function itShouldCreateANewRule()
    {
        $this->mockWriteConnection->expects($this->once())->method('insert')
            ->with(
                $this->isType('string'),
                [
                    'customer_group_id' => 5,
                    'country' => 'NZ',
                    'post_codes' => '1234,5678'
                ]
            );
        $this->storage->create('NZ', 5, ['1234', '5678']);
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
        $this->storage->delete('GB', 10);
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $mockConnection
     */
    private function addMockSelectFactory($mockConnection)
    {
        $mockConnection->expects($this->any())->method('select')->willReturnCallback(function () {
            $mock = $this->getMock(\Varien_Db_Select::class, [], [], '', false);
            $mock->expects($this->any())->method('from')->willReturnSelf();
            $mock->expects($this->any())->method('where')->willReturnSelf();
            return $mock;
        });
    }
}
