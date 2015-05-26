<?php

namespace VinaiKopp\PostCodeFilter;

use VinaiKopp\PostCodeFilter\CustomerSpecifiesShippingAddress;
use VinaiKopp\PostCodeFilter\Storage\ReadModel\RuleReader;
use VinaiKopp\PostCodeFilter\Storage\WriteModel\RuleWriter;

/**
 * @covers \VinaiKopp_PostCodeFilter_Helper_Data
 */
class DataTest extends Mage1IntegrationTestCase
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
    public function itShouldReturnARuleWriterInstance()
    {
        $this->assertInstanceOf(RuleWriter::class, $this->helper->getRuleWriter());
    }

    /**
     * @test
     */
    public function itShouldReturnARuleReaderInstance()
    {
        $this->assertInstanceOf(RuleReader::class, $this->helper->getRuleReader());
    }

    /**
     * @test
     */
    public function itShouldReturnTheCustomerChecksPostCodeUseCase()
    {
        $this->assertInstanceOf(CustomerSpecifiesShippingAddress::class,
            $this->helper->createCustomerChecksPostCodeUseCase());
    }
}
