<?php


namespace VinaiKopp\PostCodeFilter\RuleComponents;

/**
 * @covers \VinaiKopp\PostCodeFilter\RuleComponents\PostCode
 */
class PostCodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \VinaiKopp\PostCodeFilter\Exceptions\InvalidPostCodeException
     * @dataProvider invalidPostCodeProvider
     */
    public function itShouldThrowIfItIsNoIntOrString($invalidPostCode)
    {
        PostCode::fromIntOrString($invalidPostCode);
    }

    public function invalidPostCodeProvider()
    {
        return [
            'array' => [[]],
            'null' => [null],
            'float' => [0.5],
            'object' => [new \stdClass()],
            'invalid char: <' => ['<'],
            'invalid char: >' => ['>'],
            'invalid char: \'' => ['\''],
            'invalid char: "' => ['"'],
        ];
    }

    /**
     * @test
     */
    public function itShouldReturnTheValue()
    {
        $code = 69912;
        $this->assertSame($code, PostCode::fromIntOrString($code)->getValue());
    }
}
