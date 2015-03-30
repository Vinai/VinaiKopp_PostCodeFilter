<?php


namespace VinaiKopp\PostCodeFilter\UseCases;


use VinaiKopp\PostCodeFilter\Command\RuleToUpdate;
use VinaiKopp\PostCodeFilter\Command\RuleWriter;
use VinaiKopp\PostCodeFilter\Query\RuleNotFound;
use VinaiKopp\PostCodeFilter\Query\RuleReader;
use VinaiKopp\PostCodeFilter\RuleComponents\Country;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupIdList;
use VinaiKopp\PostCodeFilter\RuleComponents\PostCodeList;

/**
 * @covers \VinaiKopp\PostCodeFilter\UseCases\AdminUpdatesRule
 * @uses   \VinaiKopp\PostCodeFilter\Command\RuleToAdd
 * @uses   \VinaiKopp\PostCodeFilter\Command\RuleToDelete
 * @uses   \VinaiKopp\PostCodeFilter\Query\QueryByCountryAndGroupIds
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\Country
 */
class AdminUpdatesRuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AdminUpdatesRule
     */
    private $useCase;

    /**
     * @var RuleReader|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRuleReader;

    /**
     * @var RuleWriter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRuleWriter;

    protected function setUp()
    {
        $this->mockRuleWriter = $this->getMock(RuleWriter::class);
        $this->mockRuleReader = $this->getMock(RuleReader::class);
        $this->useCase = new AdminUpdatesRule($this->mockRuleWriter, $this->mockRuleReader);
    }

    /**
     * @test
     */
    public function itShouldOpenAndCommitATransaction()
    {
        $this->mockRuleWriter->expects($this->once())->method('beginTransaction');
        $this->mockRuleWriter->expects($this->once())->method('commitTransaction');
        $this->useCase->updateRule($this->getMockRuleToUpdate());
    }

    /**
     * @test
     * @expectedException \VinaiKopp\PostCodeFilter\Exceptions\RuleDoesNotExistException
     */
    public function itShouldThrowIfTheOriginalRuleToUpdateDoesNotExist()
    {
        $this->mockRuleReader->expects($this->once())->method('findByCountryAndGroupIds')
            ->willReturn($this->getMock(RuleNotFound::class, [], [], '', false));
        $this->useCase->updateRule($this->getMockRuleToUpdate());
    }

    /**
     * @test
     */
    public function itShouldRollbackTheTransactionIfAnExceptionIsThrown()
    {
        $testException = new \Exception('Test Exception');
        $this->mockRuleReader->expects($this->once())->method('findByCountryAndGroupIds')
            ->willThrowException($testException);
        $this->mockRuleWriter->expects($this->once())->method('rollbackTransaction');
        
        try {
            $this->useCase->updateRule($this->getMockRuleToUpdate());
        } catch (\Exception $e) {
            $this->assertSame($testException, $e);
        }
    }

    /**
     * @test
     */
    public function itShouldDeleteTheOldRule()
    {
        $this->mockRuleWriter->expects($this->once())->method('deleteRule');
        $this->useCase->updateRule($this->getMockRuleToUpdate());
    }

    /**
     * @test
     */
    public function itShouldAddTheNewRule()
    {
        $this->mockRuleWriter->expects($this->once())->method('createRule');
        $this->useCase->updateRule($this->getMockRuleToUpdate());
    }

    /**
     * @test
     */
    public function itShouldUpdateTheRuleWithScalarInputValues()
    {
        $this->mockRuleWriter->expects($this->once())->method('createRule');
        $oldIso2Country = 'DE';
        $oldCustomerGroupIds = [0, 1];
        $newCountry = 'DE';
        $newCustomerGroupIds = [0, 1, 2];
        $newPostCodes = ['10111', '10123'];
        $this->useCase->updateRuleFromScalars(
            $oldIso2Country,
            $oldCustomerGroupIds,
            $newCountry,
            $newCustomerGroupIds,
            $newPostCodes
        );
    }

    /**
     * @return RuleToUpdate|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockRuleToUpdate()
    {
        $testGroupIds = [0, 1, 2];
        $mockRuleToUpdate = $this->getMock(RuleToUpdate::class, [], [], '', false);
        $mockRuleToUpdate->expects($this->any())->method('getOldCountry')->willReturn(Country::fromIso2Code('DE'));
        $mockRuleToUpdate->expects($this->any())->method('getOldCustomerGroupIds')
            ->willReturn($this->getMock(CustomerGroupIdList::class, [], [], '', false));
        $mockRuleToUpdate->expects($this->any())->method('getOldCustomerGroupIdValues')->willReturn($testGroupIds);
        $mockRuleToUpdate->expects($this->any())->method('getNewCountry')->willReturn(Country::fromIso2Code('NZ'));
        $mockRuleToUpdate->expects($this->any())->method('getNewCustomerGroupIds')
            ->willReturn($this->getMock(CustomerGroupIdList::class, [], [], '', false));
        $mockRuleToUpdate->expects($this->any())->method('getNewPostCodes')
            ->willReturn($this->getMock(PostCodeList::class, [], [], '', false));
        return $mockRuleToUpdate;
    }
}
