<?php


namespace VinaiKopp\PostCodeFilter\UseCases;



use VinaiKopp\PostCodeFilter\Query\RuleReader;
use VinaiKopp\PostCodeFilter\Query\RuleResult;


/**
 * @covers \VinaiKopp\PostCodeFilter\UseCases\AdminListsRules
 */
class AdminListsRulesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RuleReader|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRuleReader;

    /**
     * @var AdminListsRules
     */
    private $useCase;

    public function setUp()
    {
        $this->mockRuleReader = $this->getMock(RuleReader::class);
        $this->useCase = new AdminListsRules($this->mockRuleReader);
    }

    /**
     * @test
     */
    public function itShouldFetchAllRulesFromTheReader()
    {
        $expected = [$this->getMock(RuleResult::class)];
        $this->mockRuleReader->expects($this->once())->method('findAll')->willReturn($expected);
        $this->assertSame($expected, $this->useCase->fetchAll());
    }
}
