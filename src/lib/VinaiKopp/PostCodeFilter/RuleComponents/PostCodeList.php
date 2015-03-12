<?php


namespace VinaiKopp\PostCodeFilter\RuleComponents;


class PostCodeList
{
    /**
     * @var PostCode[]
     */
    private $postCodes;

    private function __construct(array $postCodes)
    {
        $this->postCodes = $postCodes;
    }

    /**
     * @param string[]|int[]|PostCode[] $inputArray
     * @return PostCodeList
     */
    public static function fromArray(array $inputArray)
    {
        return new self(array_map([__CLASS__, 'getPostCodeInstance'], $inputArray));
    }

    /**
     * @return PostCode[]
     */
    public function getPostCodes()
    {
        return $this->postCodes;
    }

    /**
     * @return string[]|int[]
     */
    public function getValues()
    {
        return array_map(function (PostCode $postCode) {
            return $postCode->getValue();
        }, $this->postCodes);
    }

    /**
     * @param PostCode $postCode
     * @return bool
     */
    public function contains(PostCode $postCode)
    {
        return in_array($postCode, $this->postCodes);
    }

    /**
     * @param string|int|PostCode $code
     * @return PostCode
     */
    private static function getPostCodeInstance($code)
    {
        if ($code instanceof PostCode) {
            return $code;
        }
        return PostCode::fromIntOrString($code);
    }
}
