<?php


namespace VinaiKopp\PostCodeFilter;

/**
 * @covers \VinaiKopp\PostCodeFilter\CustomerGroupId
 */
class CustomerGroupIdTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \VinaiKopp\PostCodeFilter\Exception\InvalidCustomerGroupIdException
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
