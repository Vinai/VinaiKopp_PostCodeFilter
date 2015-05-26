<?php

namespace VinaiKopp\PostCodeFilter;

use VinaiKopp\PostCodeFilter\Rule\Rule;
use VinaiKopp\PostCodeFilter\Storage\ReadModel\RuleReader;

/**
 * @covers \VinaiKopp\PostCodeFilter\AdminViewsSingleRule
 * @uses   \VinaiKopp\PostCodeFilter\Storage\ReadModel\RuleSpecByCountryAndGroupIds
 * @uses   \VinaiKopp\PostCodeFilter\Rule\Components\Country
 * @uses   \VinaiKopp\PostCodeFilter\Rule\Components\CustomerGroupId
 * @uses   \VinaiKopp\PostCodeFilter\Rule\Components\CustomerGroupIdList
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
        $stubResult = $this->getMock(Rule::class);
        $this->mockRuleReader->expects($this->once())->method('findByCountryAndGroupIds')->willReturn($stubResult);
        $this->assertEquals($stubResult, $this->useCase->fetchRule('DE', ['134', '256']));
    }
}
