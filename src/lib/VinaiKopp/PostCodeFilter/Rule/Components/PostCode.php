<?php

namespace VinaiKopp\PostCodeFilter\Rule\Components;

use VinaiKopp\PostCodeFilter\Exceptions\InvalidPostCodeException;

class PostCode
{
    /**
     * @var int|string
     */
    private $postCode;
    
    private static $invalidChars = '<>\'"';

    private function __construct($postCode)
    {
        $this->postCode = $postCode;
    }

    /**
     * @param int|string $postCode
     * @return PostCode
     */
    public static function fromIntOrString($postCode)
    {
        if (! is_string($postCode) && ! is_int($postCode)) {
            throw new InvalidPostCodeException(sprintf('Each post code has to be a string or an integer'));
        }
        if (preg_match('/[' . self::$invalidChars . ']/', $postCode)) {
            throw new InvalidPostCodeException(sprintf(
                'The post code contains one of the following invalid characters: %s',
                self::$invalidChars
            ));
        }
        return new self($postCode);
    }

    /**
     * @return int|string
     */
    public function getValue()
    {
        return $this->postCode;
    }
}
