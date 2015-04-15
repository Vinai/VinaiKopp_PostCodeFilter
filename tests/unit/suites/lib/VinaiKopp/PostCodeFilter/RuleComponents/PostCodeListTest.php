<?php

namespace VinaiKopp\PostCodeFilter\RuleComponents;

/**
 * @covers \VinaiKopp\PostCodeFilter\RuleComponents\PostCodeList
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\PostCode
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
    public function itShouldReturnFalseIfAGivenPostCodeIsNotContained()
    {
        $postCodeArray = [123];
        $postCodeList = PostCodeList::fromArray($postCodeArray);
        $this->assertFalse($postCodeList->contains(PostCode::fromIntOrString(234)));
    }

    /**
     * @test
     */
    public function itShouldReturnTrueIfAGivenPostCodeIsContained()
    {
        $postCodeArray = [123];
        $postCodeList = PostCodeList::fromArray($postCodeArray);
        $this->assertTrue($postCodeList->contains(PostCode::fromIntOrString(123)));
    }

    /**
     * @test
     */
    public function itShouldFilterDuplicateValues()
    {
        $postCodeArray = [1, 2, 2, 1, 3];
        $postCodeList = PostCodeList::fromArray($postCodeArray);
        $this->assertSame([1, 2, 3], $postCodeList->getValues());
        
    }
}
