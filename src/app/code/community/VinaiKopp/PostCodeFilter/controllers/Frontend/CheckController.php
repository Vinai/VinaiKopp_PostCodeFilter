<?php


use VinaiKopp\PostCodeFilter\UseCases\CustomerChecksPostCode;

class VinaiKopp_PostCodeFilter_Frontend_CheckController extends Mage_Core_Controller_Front_Action
{
    /**
     * @var CustomerChecksPostCode
     */
    private $checkPostCodeUseCase;

    public function setCheckPostCodeUseCase(CustomerChecksPostCode $checkPostCodeUseCase)
    {
        $this->checkPostCodeUseCase = $checkPostCodeUseCase;
    }

    /**
     * @return CustomerChecksPostCode
     */
    private function getCheckPostCodeUseCase()
    {
        if (! $this->checkPostCodeUseCase) {
            // @codeCoverageIgnoreStart
            /** @var \VinaiKopp_PostCodeFilter_Helper_Data $helper */
            $helper = Mage::helper('vinaikopp_postcodefilter');
            $this->checkPostCodeUseCase = $helper->createCustomerChecksPostCodeUseCase();
        }
        // @codeCoverageIgnoreEnd
        return $this->checkPostCodeUseCase;
    }
    
    public function indexAction()
    {
        $this->getResponse()->setHeader('content-type', 'application/json', true);

        try {
            $customerGroupId = $this->getCustomerGroupId();
            $country = $this->getCountry();
            $postCode = $this->getPostCode();

            $content = $this->buildRegularResponse(
                $country,
                $postCode,
                $this->mayOrder($customerGroupId, $country, $postCode)
            );
        } catch (\Exception $e) {
            $content = $this->buildErrorResponse($e);
        }
        $this->getResponse()->setBody(json_encode($content));
    }

    /**
     * @return int
     */
    private function getCustomerGroupId()
    {
        $customerGroupId = $this->getRequest()->getParam('customer_group_id');
        if (! is_null($customerGroupId)) {
            return (int) $customerGroupId;
        }
        return Mage::getSingleton('customer/session')->getCustomerGroupId();
    }

    private function getCountry()
    {
        return (string) $this->getRequest()->getParam('country');
    }

    private function getPostCode()
    {
        return (string) $this->getRequest()->getParam('postcode');
    }

    /**
     * @param string $country
     * @param string $postCode
     * @param bool $mayOrder
     * @return mixed[]
     * @internal param int $customerGroupId
     */
    private function buildRegularResponse($country, $postCode, $mayOrder)
    {
        return [
            'country' => $country,
            'postcode' => $postCode,
            'may_order' => $mayOrder,
            'message' => $this->buildResponseMessage($mayOrder, $postCode)
        ];
    }

    /**
     * @param bool $mayOrder
     * @param string $postCode
     * @return string
     */
    private function buildResponseMessage($mayOrder, $postCode)
    {
        if ($mayOrder) {
            return '';
        } else {
            
            return $this->__(Mage::getStoreConfig('vinaikopp/postcodefilter/error_message'), $postCode);
        }
    }

    /**
     * @param \Exception $exception
     * @return mixed[]
     */
    private function buildErrorResponse(\Exception $exception)
    {
        return [
            'error' => $exception->getMessage(),
            'may_order' => false,
        ];
    }

    /**
     * @param int $customerGroupId
     * @param string $country
     * @param string $postCode
     * @return bool
     */
    private function mayOrder($customerGroupId, $country, $postCode)
    {
        return $this->getCheckPostCodeUseCase()->mayOrder($customerGroupId, $country, $postCode);
    }
}
