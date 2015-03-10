<?php


namespace VinaiKopp\PostCodeFilter;

/**
 * @covers \VinaiKopp\PostCodeFilter\RuleNotFound
 */
class RuleNotFoundTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RuleNotFound
     */
    private $rule;

    protected function setUp()
    {
        $this->rule = new RuleNotFound();
    }

    /**
     * @test
     */
    public function itShouldBeARule()
    {
        $this->assertInstanceOf(Rule::class, $this->rule);
    }

    /**
     * @test
     */
    public function itShouldReturnTrue()
    {
        $this->assertTrue($this->rule->isPostCodeAllowed('dummy'));
    }
}
