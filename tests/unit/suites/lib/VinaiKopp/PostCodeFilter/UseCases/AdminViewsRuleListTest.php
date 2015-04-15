<?php

namespace VinaiKopp\PostCodeFilter\UseCases;

use VinaiKopp\PostCodeFilter\Query\RuleReader;
use VinaiKopp\PostCodeFilter\Query\RuleResult;

/**
 * @covers \VinaiKopp\PostCodeFilter\UseCases\AdminViewsRuleList
 */
class AdminViewsRuleListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RuleReader|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRuleReader;

    /**
     * @var AdminViewsRuleList
     */
    private $useCase;

    public function setUp()
    {
        $this->mockRuleReader = $this->getMock(RuleReader::class);
        $this->useCase = new AdminViewsRuleList($this->mockRuleReader);
    }

    /**
     * @test
     */
    public function itShouldFetchAllRulesFromTheReader()
    {
        $expected = [$this->getMock(RuleResult::class)];
        $this->mockRuleReader->expects($this->once())->method('findAll')->willReturn($expected);
        $this->assertSame($expected, $this->useCase->fetchAllRules());
    }
}
