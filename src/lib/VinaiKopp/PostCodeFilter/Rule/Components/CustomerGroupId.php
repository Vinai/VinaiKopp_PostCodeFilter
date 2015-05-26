<?php

namespace VinaiKopp\PostCodeFilter\Rule\Components;

use VinaiKopp\PostCodeFilter\Exceptions\InvalidCustomerGroupIdException;

class CustomerGroupId
{
    private $id;

    /**
     * @param int $id
     */
    private function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @param int $id
     * @return CustomerGroupId
     */
    static public function fromInt($id)
    {
        if (!is_int($id)) {
            throw new InvalidCustomerGroupIdException(sprintf('The customer group ID hast to be an integer'));
        }
        return new self($id);
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->id;
    }
}
