<?php


namespace VinaiKopp\PostCodeFilter;

/**
 * @covers \VinaiKopp\PostCodeFilter\Country
 */
class CountryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \VinaiKopp\PostCodeFilter\Exception\InvalidCountryException
     */
    public function itShouldThrowIfTheCodeIsNotAString()
    {
        Country::fromCode(123);
    }
    /**
     * @test
     * @expectedException \VinaiKopp\PostCodeFilter\Exception\InvalidCountryException
     */
    public function itShouldThrowIfTheCodeIsNot2Characters()
    {
        Country::fromCode('DEU');
    }

    /**
     * @test
     */
    public function itShouldReturnACountryInstance()
    {
        $this->assertInstanceOf(Country::class, Country::fromCode('DE'));
    }

    /**
     * @test
     */
    public function itShouldReturnTheCodeInUpperCase()
    {
        $this->assertSame('DE', Country::fromCode('de')->getValue());
    }
}
