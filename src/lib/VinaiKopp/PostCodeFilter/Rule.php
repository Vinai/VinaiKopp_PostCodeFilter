<?php


namespace VinaiKopp\PostCodeFilter;


interface Rule
{
    /**
     * @param string $postCode
     * @return bool
     */
    public function isPostCodeAllowed($postCode);
}
