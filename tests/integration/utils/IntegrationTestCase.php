<?php


namespace VinaiKopp\PostCodeFilter;

class IntegrationTestCase extends \PHPUnit_Framework_TestCase
{
    public static function initMagento()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', true);
        self::includeMageFile();
        self::resetMagento();
    }
    
    final protected static function resetMagento()
    {
        \Mage::reset();
        \Mage::setIsDeveloperMode(true);
        \Mage::app();
        self::fixMagentoAutoloader();
    }
    
    private static function includeMageFile()
    {
        $dirs = [ '/../../../../../htdocs/app', '/../../../../../app'];
        foreach ($dirs as $dir) {
            $file = __DIR__ . $dir . '/Mage.php';
            if (file_exists($file)) {
                return require $file;
            }
        }
        throw new \RuntimeException('Unable to locate app/Mage.php');
    }

    private static function fixMagentoAutoloader()
    {
        $magentoHandler = self::getCurrentErrorHandler();
        set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($magentoHandler) {
            if (E_WARNING === $errno &&
                0 === strpos($errstr, 'include(')
                && substr($errfile, -19) == 'Varien/Autoload.php')
            {
                $hideErrorReturnValue = null;
                return $hideErrorReturnValue;
            }
            return call_user_func($magentoHandler, $errno, $errstr, $errfile, $errline);
        });
    }

    private static function getCurrentErrorHandler()
    {
        return set_error_handler(function () {
            $useNativeHandlerReturnValue = false;
            return $useNativeHandlerReturnValue;
        });
    }
}
