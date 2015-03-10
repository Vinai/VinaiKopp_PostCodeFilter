<?php


namespace VinaiKopp\PostCodeFilter;

/**
 * @covers \VinaiKopp\PostCodeFilter\RuleFound
 */
class RuleFoundTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RuleFound
     */
    private $rule;
    
    private $matchingPostCode = '1234';

    protected function setUp()
    {
        $this->rule = new RuleFound([$this->matchingPostCode]);
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
    public function itShouldReturnFalseIfThePostCodeIsNotFound()
    {
        $this->assertFalse($this->rule->isPostCodeAllowed('dummy'));
    }

    /**
     * @test
     */
    public function itShouldReturnTrueIfThePostCodeIsAllowed()
    {
        $this->assertTrue($this->rule->isPostCodeAllowed($this->matchingPostCode));
    }

    /**
     * @test
     * @expectedException \VinaiKopp\PostCodeFilter\Exception\InvalidPostCodeException
     */
    public function itShouldThrowIfThePostCodeIsNoScalar()
    {
        $postCode = [];
        $this->rule->isPostCodeAllowed($postCode);
    }
}
