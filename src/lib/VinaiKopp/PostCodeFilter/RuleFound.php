<?php


namespace VinaiKopp\PostCodeFilter;

use VinaiKopp\PostCodeFilter\Exception\InvalidPostCodeException;

class RuleFound implements Rule
{
    /**
     * @var string[]
     */
    private $allowedPostCodes;

    /**
     * @param string[] $allowedPostCodes
     */
    public function __construct(array $allowedPostCodes)
    {
        $this->allowedPostCodes = $allowedPostCodes;
    }
    
    /**
     * @param string $postCode
     * @return bool
     */
    public function isPostCodeAllowed($postCode)
    {
        $this->validatePostCode($postCode);
        return in_array($postCode, $this->allowedPostCodes);
    }
    
    /**
     * @param string|int $postCode
     * @throws InvalidPostCodeException
     */
    private function validatePostCode($postCode)
    {
        if (!is_string($postCode) && ! is_int($postCode)) {
            throw new InvalidPostCodeException('The postcode has to be a string or an integer value');
        }
    }
}
