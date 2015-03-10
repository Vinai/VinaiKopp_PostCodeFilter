<?php


namespace VinaiKopp\PostCodeFilter;

/**
 * @covers \VinaiKopp\PostCodeFilter\PostCode
 */
class PostCodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \VinaiKopp\PostCodeFilter\Exception\InvalidPostCodeException
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
