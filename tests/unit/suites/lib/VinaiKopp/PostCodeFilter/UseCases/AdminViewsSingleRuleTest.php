<?php

namespace VinaiKopp\PostCodeFilter\UseCases;

use VinaiKopp\PostCodeFilter\Query\RuleReader;
use VinaiKopp\PostCodeFilter\Query\RuleResult;

/**
 * @covers \VinaiKopp\PostCodeFilter\UseCases\AdminViewsSingleRule
 * @uses   \VinaiKopp\PostCodeFilter\Query\RuleSpecByCountryAndGroupIds
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\Country
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupId
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupIdList
 */
class AdminViewsSingleRuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RuleReader|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRuleReader;

    /**
     * @var AdminViewsSingleRule
     */
    private $useCase;

    protected function setUp()
    {
        $this->mockRuleReader = $this->getMock(RuleReader::class);
        $this->useCase = new AdminViewsSingleRule($this->mockRuleReader);
    }

    /**
     * @test
     */
    public function itShouldReturnASingeRule()
    {
        $stubResult = $this->getMock(RuleResult::class);
        $this->mockRuleReader->expects($this->once())->method('findByCountryAndGroupIds')->willReturn($stubResult);
        $this->assertEquals($stubResult, $this->useCase->fetchRule('DE', ['134', '256']));
    }
}
