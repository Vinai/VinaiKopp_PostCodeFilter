<?php


namespace VinaiKopp\PostCodeFilter\RuleComponents;

/**
 * @covers \VinaiKopp\PostCodeFilter\RuleComponents\Country
 */
class CountryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \VinaiKopp\PostCodeFilter\Exceptions\InvalidCountryException
     */
    public function itShouldThrowIfTheCodeIsNotAString()
    {
        Country::fromCode(123);
    }
    /**
     * @test
     * @expectedException \VinaiKopp\PostCodeFilter\Exceptions\InvalidCountryException
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
