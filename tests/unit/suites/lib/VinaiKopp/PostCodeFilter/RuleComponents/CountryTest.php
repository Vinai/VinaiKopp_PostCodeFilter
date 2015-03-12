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
        Country::fromIso2Code(123);
    }
    /**
     * @test
     * @expectedException \VinaiKopp\PostCodeFilter\Exceptions\InvalidCountryException
     */
    public function itShouldThrowIfTheCodeIsNot2Characters()
    {
        Country::fromIso2Code('DEU');
    }

    /**
     * @test
     */
    public function itShouldReturnACountryInstance()
    {
        $this->assertInstanceOf(Country::class, Country::fromIso2Code('DE'));
    }

    /**
     * @test
     */
    public function itShouldReturnTheCodeInUpperCase()
    {
        $this->assertSame('DE', Country::fromIso2Code('de')->getValue());
    }
}
