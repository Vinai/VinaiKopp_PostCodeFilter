<?php


namespace VinaiKopp\PostCodeFilter\RuleComponents;

/**
 * @covers \VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupId
 */
class CustomerGroupIdTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \VinaiKopp\PostCodeFilter\Exceptions\InvalidCustomerGroupIdException
     */
    public function itShouldThrowIfTheIdIsNotAnInt()
    {
        CustomerGroupId::fromInt('123');
    }

    /**
     * @test
     */
    public function itShouldReturnACustomerGroupIdInstance()
    {
        $this->assertInstanceOf(CustomerGroupId::class, CustomerGroupId::fromInt(123));
    }

    /**
     * @test
     */
    public function itShouldReturnTheValue()
    {
        $testValue = 123;
        $this->assertSame($testValue, CustomerGroupId::fromInt($testValue)->getValue());
    }
}
