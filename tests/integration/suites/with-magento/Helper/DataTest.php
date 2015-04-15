<?php

namespace VinaiKopp\PostCodeFilter;

use VinaiKopp\PostCodeFilter\Command\RuleToAdd;
use VinaiKopp\PostCodeFilter\Command\RuleToDelete;
use VinaiKopp\PostCodeFilter\Command\RuleToUpdate;
use VinaiKopp\PostCodeFilter\Command\RuleToUpdateTest;
use VinaiKopp\PostCodeFilter\Query\RuleSpecByCountryAndGroupIds;
use VinaiKopp\PostCodeFilter\UseCases\CustomerChecksPostCode;
use VinaiKopp\PostCodeFilter\UseCases\CustomerChecksPostCodeTest;

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
    public function itShouldReturnARepositoryInstanceAsWriter()
    {
        $this->assertInstanceOf(RuleRepository::class, $this->helper->getRuleWriter());
    }

    /**
     * @test
     */
    public function itShouldReturnARepositoryInstanceAsReader()
    {
        $this->assertInstanceOf(RuleRepository::class, $this->helper->getRuleReader());
    }

    /**
     * @test
     */
    public function itShouldReturnARuleSpecByCountry()
    {
        $this->assertInstanceOf(
            RuleSpecByCountryAndGroupIds::class,
            $this->helper->createRuleSpecForGroupIdsAndCountry([11], 'DE')
        );
    }

    /**
     * @test
     */
    public function itShouldReturnTheCustomerChecksPostCodeUseCase()
    {
        $this->assertInstanceOf(CustomerChecksPostCode::class, $this->helper->createCustomerChecksPostCodeUseCase());
    }
}
