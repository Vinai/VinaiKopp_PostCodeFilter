<?php

namespace VinaiKopp\PostCodeFilter;

use VinaiKopp\PostCodeFilter\Command\RuleToAdd;
use VinaiKopp\PostCodeFilter\Command\RuleToDelete;
use VinaiKopp\PostCodeFilter\Query\QueryByCountryAndGroupIds;

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
        $this->assertInstanceOf(RuleRepository::class, $this->helper->getRuleWriter());
    }

    /**
     * @test
     */
    public function itShouldReturnARuleToAdd()
    {
        $this->assertInstanceOf(RuleToAdd::class, $this->helper->createRuleToAdd([11], 'DE', ['1', '2']));
    }

    /**
     * @test
     */
    public function itShouldReturnARuleToDelete()
    {
        $this->assertInstanceOf(RuleToDelete::class, $this->helper->createRuleToDelete([11], 'DE'));
    }

    /**
     * @test
     */
    public function itShouldReturnARuleQueryByCountry()
    {
        $this->assertInstanceOf(
            QueryByCountryAndGroupIds::class,
            $this->helper->createRuleQueryForGroupIdsAndCountry([11], 'DE')
        );
    }
}
