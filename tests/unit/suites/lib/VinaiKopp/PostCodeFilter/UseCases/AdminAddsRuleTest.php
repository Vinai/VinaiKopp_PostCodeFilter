<?php

namespace VinaiKopp\PostCodeFilter\UseCases;

use VinaiKopp\PostCodeFilter\ReadModel\RuleFound;
use VinaiKopp\PostCodeFilter\ReadModel\RuleReader;
use VinaiKopp\PostCodeFilter\RuleComponents\Country;
use VinaiKopp\PostCodeFilter\WriteModel\RuleToAdd;
use VinaiKopp\PostCodeFilter\WriteModel\RuleWriter;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupIdList;

/**
 * @covers \VinaiKopp\PostCodeFilter\UseCases\AdminAddsRule
 * @uses   \VinaiKopp\PostCodeFilter\ReadModel\RuleFound
 * @uses   \VinaiKopp\PostCodeFilter\ReadModel\RuleSpecByCountryAndGroupIds
 * @uses   \VinaiKopp\PostCodeFilter\WriteModel\RuleToAdd
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\Country
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupId
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupIdList
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\PostCode
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\PostCodeList
 */
class AdminAddsRuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AdminAddsRule
     */
    private $useCase;

    /**
     * @var RuleWriter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRuleWriter;

    /**
     * @var RuleReader|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRuleReader;

    protected function setUp()
    {
        $this->mockRuleWriter = $this->getMock(RuleWriter::class);
        $this->mockRuleReader = $this->getMock(RuleReader::class);
        $this->useCase = new AdminAddsRule($this->mockRuleWriter, $this->mockRuleReader);
    }

    /**
     * @test
     */
    public function itShouldDelegateToTheRepository()
    {
        $stubRuleToAdd = $this->createStubRuleToAdd();
        $this->mockRuleReader->expects($this->once())
            ->method('findByCountryAndGroupIds');
        $this->mockRuleWriter->expects($this->once())
            ->method('createRule')
            ->with($stubRuleToAdd);

        $this->useCase->addRule($stubRuleToAdd);
    }

    /**
     * @test
     * @expectedException \VinaiKopp\PostCodeFilter\Exceptions\RuleForGroupAndCountryAlreadyExistsException
     */
    public function itShouldThrowIfRuleForGroupAndCountryAlreadyExist()
    {
        $stubRuleFound = $this->getMock(RuleFound::class, [], [], '', false);
        $stubRuleFound->method('getCustomerGroupIdValues')->willReturn([1, 2]);
        $this->mockRuleReader->expects($this->once())
            ->method('findByCountryAndGroupIds')
            ->willReturn($stubRuleFound);

        $stubRuleToAdd = $this->createStubRuleToAdd();
        $this->mockRuleWriter->expects($this->never())
            ->method('createRule')
            ->with($stubRuleToAdd);

        $this->useCase->addRule($stubRuleToAdd);
    }

    /**
     * @test
     */
    public function itShouldOpenATransactionAndCommitIt()
    {
        $this->mockRuleWriter->expects($this->once())->method('beginTransaction');
        $this->mockRuleWriter->expects($this->once())->method('commitTransaction');
        $this->useCase->addRule($this->createStubRuleToAdd());
    }

    /**
     * @test
     */
    public function itShouldAbortATransactionIfAnExceptionIsThrown()
    {
        $testException = new \Exception('Test Exception');
        $this->mockRuleReader->method('findByCountryAndGroupIds')
            ->willThrowException($testException);
        $this->mockRuleWriter->expects($this->once())->method('rollbackTransaction');
        try {
            $this->useCase->addRule($this->createStubRuleToAdd());
        } catch (\Exception $e) {
            $this->assertSame($testException, $e);
        }
    }

    /**
     * @test
     */
    public function itShouldAddARuleUsingScalarInput()
    {
        $this->mockRuleWriter->expects($this->once())->method('createRule');
        $customerGroupIds = [1, 2];
        $iso2Country = 'DE';
        $postCodes = ['10115'];
        $this->useCase->addRuleFromScalars($customerGroupIds, $iso2Country, $postCodes);
    }

    /**
     * @return RuleToAdd|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createStubRuleToAdd()
    {
        $stubRuleToAdd = $this->getMock(RuleToAdd::class, [], [], '', false);
        $stubRuleToAdd->method('getCustomerGroupIds')->willReturn(
            $this->getMock(CustomerGroupIdList::class, [], [], '', false)
        );
        $stubRuleToAdd->method('getCountry')->willReturn(
            $this->getMock(Country::class, [], [], '', false)
        );
        return $stubRuleToAdd;
    }
}
