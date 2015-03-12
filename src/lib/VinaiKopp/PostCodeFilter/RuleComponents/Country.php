<?php


namespace VinaiKopp\PostCodeFilter\RuleComponents;

use VinaiKopp\PostCodeFilter\Exceptions\InvalidCountryException;

class Country
{
    /**
     * @var string
     */
    private $code;

    /**
     * @param string $code
     */
    private function __construct($code)
    {
        $this->code = $code;
    }

    /**
     * @param string $code
     * @return Country
     */
    public static function fromCode($code)
    {
        if (! is_string($code)) {
            throw new InvalidCountryException(sprintf('The country code has to be a string'));
        }
        if (strlen($code) != 2) {
            throw new InvalidCountryException(sprintf('The country code has to be two characters long'));
        }
        
        return new self(strtoupper($code));
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->code;
    }
}
