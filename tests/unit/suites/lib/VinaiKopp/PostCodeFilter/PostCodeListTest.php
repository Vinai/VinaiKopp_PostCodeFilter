<?php


namespace VinaiKopp\PostCodeFilter;

/**
 * @covers \VinaiKopp\PostCodeFilter\PostCodeList
 * @uses   \VinaiKopp\PostCodeFilter\PostCode
 */
class PostCodeListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function itShouldBeConstructableWithPostCodes()
    {
        $stubPostCode = $this->getMock(PostCode::class, [], [], '', false);
        $this->assertInstanceOf(PostCodeList::class, PostCodeList::fromArray([$stubPostCode]));
    }
    
    /**
     * @test
     */
    public function itShouldBeConstructableWithStringCodes()
    {
        $stringPostCode = '123456';
        $this->assertInstanceOf(PostCodeList::class, PostCodeList::fromArray([$stringPostCode]));
    }
    
    /**
     * @test
     */
    public function itShouldBeConstructableWithIntegerCodes()
    {
        $intPostCode = 123456;
        $this->assertInstanceOf(PostCodeList::class, PostCodeList::fromArray([$intPostCode]));
    }

    /**
     * @test
     */
    public function itShouldReturnThePostCodes()
    {
        $postCodeArray = [
            PostCode::fromIntOrString('123'),
            '456',
            789,
        ];
        $postCodeList = PostCodeList::fromArray($postCodeArray);
        $this->assertContainsOnly(PostCode::class, $postCodeList->getPostCodes());
        $this->assertCount(count($postCodeArray), $postCodeList->getPostCodes());
    }

    /**
     * @test
     */
    public function itShouldReturnTheRawPostCodeValues()
    {
        $postCodeArray = [
            PostCode::fromIntOrString('123'),
            '456',
            789,
        ];
        $postCodeList = PostCodeList::fromArray($postCodeArray);
        $this->assertSame(['123', '456', 789], $postCodeList->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeTraversable()
    {
        $inputArray = ['AAAA', 'BBBB'];
        $postCodeList = PostCodeList::fromArray($inputArray);
        $this->assertInstanceOf(\IteratorAggregate::class, $postCodeList);
        
        /**
         * @var PostCode $postCode
         */
        foreach ($postCodeList as $i => $postCode) {
            $this->assertEquals($inputArray[$i], $postCode->getValue());
        }
    }
}
