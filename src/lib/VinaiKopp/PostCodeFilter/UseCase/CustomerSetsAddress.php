<?php


namespace VinaiKopp\PostCodeFilter\UseCase;

use VinaiKopp\PostCodeFilter\Country;
use VinaiKopp\PostCodeFilter\CustomerGroupId;
use VinaiKopp\PostCodeFilter\RuleQuery;
use VinaiKopp\PostCodeFilter\RuleRepository;

class CustomerSetsAddress
{
    /**
     * @var RuleRepository
     */
    private $repository;

    public function __construct(RuleRepository $repository)
    {
        $this->repository = $repository;
    }
    
    /**
     * @param int $customerGroupId
     * @param string $country
     * @param string $postCode
     * @return bool
     */
    public function mayOrder($customerGroupId, $country, $postCode)
    {
        $query = new RuleQuery(CustomerGroupId::fromInt($customerGroupId), Country::fromCode($country));
        $rule = $this->repository->findByGroupAndCountry($query);
        return $rule->isPostCodeAllowed($postCode);
    }
}
