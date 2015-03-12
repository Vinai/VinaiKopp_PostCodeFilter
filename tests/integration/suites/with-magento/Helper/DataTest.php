<?php

namespace VinaiKopp\PostCodeFilter;

/**
 * @covers \VinaiKopp_PostCodeFilter_Helper_Data
 */
class DataTest extends IntegrationTestCase
{
    /**
     * @var \VinaiKopp_PostCodeFilter_Helper_Data
     */
    private $helper;

    public function setUp()
    {
        $this->helper = new \VinaiKopp_PostCodeFilter_Helper_Data();
    }

    /**
     * @test
     */
    public function itShouldBeAHelper()
    {
        $this->assertInstanceOf(\Mage_Core_Helper_Abstract::class, $this->helper);
    }

    /**
     * @test
     */
    public function itShouldReturnARepositoryInstance()
    {
        $this->assertInstanceOf(RuleRepository::class, $this->helper->getRuleRepository());
    }

    /**
     * @test
     */
    public function itShouldReturnARuleToAdd()
    {
        $this->assertInstanceOf(RuleToAdd::class, $this->helper->createRuleToAdd(11, 'DE', ['1', '2']));
    }

    /**
     * @test
     */
    public function itShouldReturnARuleToDelete()
    {
        $this->assertInstanceOf(RuleToDelete::class, $this->helper->createRuleToDelete(11, 'DE'));
    }

    /**
     * @test
     */
    public function itShouldReturnARuleQueryByCountry()
    {
        $this->assertInstanceOf(RuleQueryByCountryAndGroupIds::class, $this->helper->createRuleQueryForCountry('DE'));
    }
}
