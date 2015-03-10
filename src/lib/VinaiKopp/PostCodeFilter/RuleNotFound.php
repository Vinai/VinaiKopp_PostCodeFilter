<?php


namespace VinaiKopp\PostCodeFilter;

class RuleNotFound implements Rule
{
    /**
     * @param string $postCode
     * @return bool
     */
    public function isPostCodeAllowed($postCode)
    {
        return true;
    }
}
