<?php


namespace VinaiKopp\PostCodeFilter;


use VinaiKopp\PostCodeFilter\Exception\InvalidPostCodeException;

class PostCode
{
    /**
     * @var int|string
     */
    private $postCode;

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
