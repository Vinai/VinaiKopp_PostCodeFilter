<?php

use VinaiKopp\PostCodeFilter\AdminAddsRule;
use VinaiKopp\PostCodeFilter\AdminDeletesRule;
use VinaiKopp\PostCodeFilter\AdminUpdatesRule;
use VinaiKopp\PostCodeFilter\AdminViewsSingleRule;

class VinaiKopp_PostCodeFilter_Adminhtml_Vinaikopp_PostcodefilterController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * @var AdminAddsRule
     */
    private $addRuleUseCase;

    /**
     * @var AdminDeletesRule
     */
    private $deleteRuleUseCase;

    /**
     * @var AdminUpdatesRule
     */
    private $updateUseCase;

    /**
     * @var AdminViewsSingleRule
     */
    private $viewsRuleUseCase;
    
    /**
     * @var VinaiKopp_PostCodeFilter_Helper_Factory
     */
    private $helper;


    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function createAction()
    {
        try {
            $country = $this->getPostedCountry();
            $customerGroupIds = $this->getPostedCustomerGroupIds();
            $postCodes = $this->getPostedPostCodes();
            $this->addRule($customerGroupIds, $country, $postCodes);
            $this->_getSession()->addSuccess($this->__('Post Code Filter Rule saved'));
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($this->__('Error: %s', $e->getMessage()));
        }
        $this->_redirect('*/*/index');
    }

    public function editAction()
    {
        try {
            $country = $this->getCountryParam();
            $customerGroupIds = $this->getCustomerGroupIdsParam();
            $rule = $this->getViewSingleRuleUseCase()->fetchRule($country, $customerGroupIds);
            Mage::register('current_rule', new Varien_Object([
                'country' => $rule->getCountryValue(),
                'customer_groups' => $rule->getCustomerGroupIdValues(),
                'post_codes' => $rule->getPostCodeValues()
            ]));
            
            $this->loadLayout();
            $this->renderLayout();
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($this->__('Error: %s', $e->getMessage()));
            $this->_redirect('*/*/index');
        }
    }

    public function updateAction()
    {
        try {
            $this->updateRule(
                $this->getOldCountry(),
                $this->getOldCustomerGroupIds(),
                $this->getPostedCountry(),
                $this->getPostedCustomerGroupIds(),
                $this->getPostedPostCodes()
            );
            $this->_getSession()->addSuccess($this->__('Post Code Filter Rule updated'));
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($this->__('Error: %s', $e->getMessage()));
        }
        $this->_redirect('*/*/index');
    }

    public function deleteAction()
    {
        try {
            $this->deleteRule($this->getCustomerGroupIdsParam(), $this->getCountryParam());
            $this->_getSession()->addSuccess($this->__('Post Code Filter Rule deleted'));
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($this->__('Error: %s', $e->getMessage()));
        }
        $this->_redirect('*/*/index');
    }

    /**
     * @return VinaiKopp_PostCodeFilter_Helper_Factory
     */
    private function getHelper()
    {
        if (is_null($this->helper)) {
            $this->helper = Mage::helper('vinaikopp_postcodefilter/factory');
        }
        return $this->helper;
    }

    public function setAddUseCase(AdminAddsRule $addUseCase)
    {
        $this->addRuleUseCase = $addUseCase;
    }

    private function getAddUseCase()
    {
        if (! $this->addRuleUseCase) {
            // @codeCoverageIgnoreStart
            $ruleWriter = $this->getHelper()->getRuleWriter();
            $ruleReader = $this->getHelper()->getRuleWriter();
            $this->addRuleUseCase = new AdminAddsRule($ruleWriter, $ruleReader);
        }
        // @codeCoverageIgnoreEnd
        return $this->addRuleUseCase;
    }

    public function setDeleteUseCase(AdminDeletesRule $deleteUseCase)
    {
        $this->deleteRuleUseCase = $deleteUseCase;
    }

    private function getDeleteUseCase()
    {
        if (! $this->deleteRuleUseCase) {
            // @codeCoverageIgnoreStart
            $ruleWriter = $this->getHelper()->getRuleWriter();
            $ruleReader = $this->getHelper()->getRuleReader();
            $this->deleteRuleUseCase = new AdminDeletesRule($ruleWriter, $ruleReader);
        }
        // @codeCoverageIgnoreEnd
        return $this->deleteRuleUseCase;
    }

    public function setUpdateRuleUseCase(AdminUpdatesRule $updateUseCase)
    {
        $this->updateUseCase = $updateUseCase;
    }
    
    private function getUpdateUseCase()
    {
        if (! $this->updateUseCase) {
            // @codeCoverageIgnoreStart
            $ruleWriter = $this->getHelper()->getRuleWriter();
            $ruleReader = $this->getHelper()->getRuleReader();
            $this->updateUseCase = new AdminUpdatesRule($ruleWriter, $ruleReader);
        }
        // @codeCoverageIgnoreEnd
        return $this->updateUseCase;
    }

    public function setViewSingleRuleUseCase(AdminViewsSingleRule $viewsRuleUseCase)
    {
        $this->viewsRuleUseCase = $viewsRuleUseCase;
    }

    private function getViewSingleRuleUseCase()
    {
        if (! $this->viewsRuleUseCase) {
            // @codeCoverageIgnoreStart
            $ruleReader = $this->getHelper()->getRuleReader();
            $this->viewsRuleUseCase = new AdminViewsSingleRule($ruleReader);
            // @codeCoverageIgnoreEnd
        }
        return $this->viewsRuleUseCase;
    }

    /**
     * @param int[] $customerGroupIds
     * @param string $country
     * @param string[]|int[] $postCodes
     */
    private function addRule(array $customerGroupIds, $country, array $postCodes)
    {
        $this->getAddUseCase()->addRuleFromScalars(
            array_map('intval', $customerGroupIds),
            $country,
            $postCodes
        );
    }

    private function updateRule($oldCountry, $oldCustomerGroupIds, $newCountry, $newCustomerGroupIds, $newPostCodes)
    {
        $this->getUpdateUseCase()->updateRuleFromScalars(
            $oldCountry,
            array_map('intval', $oldCustomerGroupIds),
            $newCountry,
            array_map('intval', $newCustomerGroupIds),
            $newPostCodes
        );
    }

    /**
     * @param int[] $customerGroupIds
     * @param string $country
     */
    private function deleteRule(array $customerGroupIds, $country)
    {
        $this->getDeleteUseCase()->deleteRuleFromScalars(
            array_map('intval', $customerGroupIds),
            $country
        );
    }

    /**
     * @return string[]
     */
    private function getPostedPostCodes()
    {
        return $this->splitParam($this->getRequest()->getPost('post_codes'));
    }

    /**
     * @return mixed
     */
    private function getPostedCustomerGroupIds()
    {
        return $this->getRequest()->getPost('customer_group_ids');
    }

    /**
     * @return mixed
     */
    private function getPostedCountry()
    {
        return $this->getRequest()->getPost('country');
    }

    /**
     * @return string
     */
    private function getOldCountry()
    {
        return $this->getRequest()->getParam('old_country');
    }

    /**
     * @return string[]
     */
    private function getOldCustomerGroupIds()
    {
        return $this->splitParam($this->getRequest()->getParam('old_customer_group_ids'));
    }

    /**
     * @return string[]
     */
    private function getCustomerGroupIdsParam()
    {
        return $this->splitParam($this->getRequest()->getParam('customer_group_ids'));
    }

    private function getCountryParam()
    {
        return $this->getRequest()->getParam('country');
    }

    /**
     * @param $paramString
     * @return array
     */
    private function splitParam($paramString)
    {
        return preg_split('/,/', $paramString, null, PREG_SPLIT_NO_EMPTY);
    }
}
