<?php


namespace VinaiKopp\PostCodeFilter;

/**
 * @coversNothing
 */
class MageConfigTest extends IntegrationTestCase
{
    private $moduleName = 'VinaiKopp_PostCodeFilter';

    /**
     * @var string
     */
    private $classGroup;

    public static function setUpBeforeClass()
    {
        self::resetMagento();
    }

    public function setUp()
    {
        $this->classGroup = strtolower($this->moduleName);
    }
    
    /**
     * @test
     */
    public function itShouldBeKnownToMagento()
    {
        $this->assertModuleIsKnown($this->moduleName);
        $this->assertCodePool($this->moduleName, 'community');
    }

    /**
     * @test
     */
    public function itShouldRegisterAModelClassGroup()
    {
        $testClassName = \Mage::getConfig()->getModelClassName($this->classGroup . '/test');
        $this->assertEquals($this->moduleName . '_Model_Test', $testClassName);
    }

    /**
     * @test
     */
    public function itShouldRegisterAResourceClassGroup()
    {
        $testClassName = \Mage::getConfig()->getResourceModelClassName($this->classGroup . '/test');
        $this->assertEquals($this->moduleName . '_Model_Resource_Test', $testClassName);
    }

    /**
     * @test
     */
    public function itShouldRegisterAHelperClassGroup()
    {
        $testClassName = \Mage::getConfig()->getHelperClassName($this->classGroup . '/test');
        $this->assertEquals($this->moduleName . '_Helper_Test', $testClassName);
    }

    /**
     * @test
     */
    public function itShouldRegisterABlockClassGroup()
    {
        $testClassName = \Mage::getConfig()->getBlockClassName($this->classGroup . '/test');
        $this->assertEquals($this->moduleName . '_Block_Test', $testClassName);
    }

    /**
     * @test
     */
    public function itShouldRegisterAnAdminLayoutFile()
    {
        $value = (string) \Mage::getConfig()->getNode('adminhtml/layout/updates/' . $this->classGroup . '/file');
        $this->assertEquals('vinaikopp/postcodefilter.xml', $value);
        $file = \Mage::getBaseDir('design') . '/adminhtml/base/default/layout/' . $value;
        $this->assertFileExists($file);
    }

    /**
     * @test
     */
    public function itShouldAddItselfToTheAdminRoute()
    {
        $front = \Mage::app()->getFrontController();
        /** @var \Mage_Core_Controller_Varien_Router_Admin $route */
        $route = $front->getRouterByRoute('admin');
        $frontName = $route->getFrontNameByRoute('adminhtml');
        $modules = $route->getModuleByFrontName($frontName);
        $this->assertContains($this->moduleName . '_Adminhtml', $modules);
    }

    /**
     * @test
     */
    public function itShouldAddAMenuNode()
    {
        $config = \Mage::getConfig()->loadModulesConfiguration('adminhtml.xml');
        $node = $config->getNode('menu/customer/children/' . $this->classGroup);
        $this->assertNotFalse($node, sprintf('%s adminhtml.xml not loaded', $this->moduleName));
        $this->assertNotEmpty((string) $node->title);
        $this->assertEquals('adminhtml/' . $this->classGroup . '/index', (string) $node->action);
    }

    /**
     * @test
     */
    public function itShouldAddAnAclRecordForTheAdminhtmlRoute()
    {
        $config = \Mage::getConfig()->loadModulesConfiguration('adminhtml.xml');
        $node = $config->getNode('acl/resources/admin/children/customer/children/' . $this->classGroup);
        $this->assertNotFalse($node, sprintf('%s adminhtml.xml not loaded', $this->moduleName));
        $this->assertNotEmpty((string) $node->title);
    }

    /**
     * @test
     */
    public function itShouldHaveASetupResource()
    {
        $node = \Mage::getConfig()->getNode('global/resources/' . $this->classGroup . '_setup/setup');
        $this->assertNotFalse($node);
        $this->assertEquals($this->moduleName, (string) $node->module);
    }

    /**
     * @test
     */
    public function itShouldHaveAVersion()
    {
        $node = \Mage::getConfig()->getNode('modules/' . $this->moduleName. '/version');
        $this->assertNotFalse($node);
    }

    /**
     * @test
     */
    public function itShouldHaveASetupDirectory()
    {
        $dir = \Mage::getModuleDir('sql', $this->moduleName);
        $this->assertFileExists($dir . '/' . $this->classGroup . '_setup');
    }

    /**
     * @test
     */
    public function itShouldDeclareTheTableName()
    {
        $configPath = 'global/models/' . $this->classGroup . '_resource/entities/rule/table';
        $node = \Mage::getConfig()->getNode($configPath);
        $this->assertNotFalse($node, "No table name declaration found");
        $this->assertEquals('vinaikopp_postcodefilter_rule', (string) $node);
    }

    /**
     * @param string $moduleName
     */
    private function assertModuleIsKnown($moduleName)
    {
        $node = \Mage::getConfig()->getNode('modules/' . $moduleName);
        if (false === $node) {
            $this->fail(sprintf('The module "%s" is not registered', $moduleName));
        }
        $this->assertSame('true', (string) $node->active, sprintf(
                'The module "%s" is not active', $moduleName)
        );
    }

    /**
     * @param string $moduleName
     * @param string $expectedCodePool
     */
    private function assertCodePool($moduleName, $expectedCodePool)
    {
        $codePool = (string) \Mage::getConfig()->getNode('modules/' . $moduleName . '/codePool');
        $this->assertEquals($expectedCodePool, $codePool, sprintf(
            'The codePool for the module "%s" is not "%s"', $moduleName, $expectedCodePool
        ));
    }
}
