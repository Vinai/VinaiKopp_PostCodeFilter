<?php

namespace VinaiKopp\PostCodeFilter;

/**
 * @covers \VinaiKopp_PostCodeFilter_Model_Resource_PostCodeFilter_Collection
 */
class PostCodeFilterCollectionTest extends IntegrationTestCase
{
    /**
     * @var \VinaiKopp_PostCodeFilter_Model_Resource_PostCodeFilter_Collection
     */
    private $collection;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RuleRepository
     */
    private $mockRepository;

    protected function setUp()
    {
        $this->mockRepository = $this->getMock(RuleRepository::class, [], [], '', false);
        $this->collection = new \VinaiKopp_PostCodeFilter_Model_Resource_PostCodeFilter_Collection(
            $this->mockRepository
        );
    }

    /**
     * @test
     */
    public function itShouldBeAVarienDataCollection()
    {
        $this->assertInstanceOf(\Varien_Data_Collection::class, $this->collection);
    }

    /**
     * @test
     */
    public function itShouldBeInstantiableByMagento()
    {
        $instance = \Mage::getResourceModel('vinaikopp_postcodefilter/postCodeFilter_collection');
        $this->assertInstanceOf(\Varien_Data_Collection::class, $instance);
    }

    /**
     * @test
     */
    public function itShouldDelegateOnceToTheRepositoryToFindAllRules()
    {
        $this->mockRepository->expects($this->once())->method('findAll')
            ->willReturn([new RuleFound(
                CustomerGroupId::fromInt(0),
                Country::fromCode('DE'),
                PostCodeList::fromArray([123, 999])
            )]);;
        $this->collection->load();
        $this->collection->load();
        $this->assertCount(1, $this->collection);
    }

    /**
     * @test
     */
    public function itShouldLoadASpecificRule()
    {
        $this->markTestSkipped();
        $this->mockRepository->expects($this->once())->method('findByCountry')
            ->with($this->isInstanceOf(RuleQueryByCountryAndGroupIds::class))
            ->willReturn([new RuleFound(
                CustomerGroupId::fromInt(0),
                Country::fromCode('DE'),
                PostCodeList::fromArray([123, 999])
            )]);
        $this->collection->loadByCountry('DE');
        $this->assertCount(1, $this->collection);
    }
}
