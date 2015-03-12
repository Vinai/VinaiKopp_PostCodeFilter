<?php

use VinaiKopp\PostCodeFilter\UseCases\AdminAddsRule;
use VinaiKopp\PostCodeFilter\UseCases\AdminDeletesRule;
use VinaiKopp\PostCodeFilter\UseCases\AdminUpdatesRule;
use VinaiKopp\PostCodeFilter\UseCases\AdminViewsSingleRule;

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
     * @var VinaiKopp_PostCodeFilter_Helper_Data
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
            Mage::register('current_rule', $rule);
            
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
            $ruleToUpdate =  $this->getHelper()->createRuleToUpdate(
                $this->getOldCountry(),
                $this->getOldCustomerGroupIds(),
                $this->getPostedCountry(),
                $this->getPostedCustomerGroupIds(),
                $this->getPostedPostCodes()
            );
            $this->getUpdateUseCase()->updateRule($ruleToUpdate);
            
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
     * @return VinaiKopp_PostCodeFilter_Helper_Data
     */
    private function getHelper()
    {
        if (is_null($this->helper)) {
            $this->helper = Mage::helper('vinaikopp_postcodefilter');
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
        $ruleToAdd = $this->getHelper()->createRuleToAdd($customerGroupIds, $country, $postCodes);
        $this->getAddUseCase()->addRule($ruleToAdd);
    }

    /**
     * @param int[] $customerGroupIds
     * @param string $country
     */
    private function deleteRule(array $customerGroupIds, $country)
    {
        $ruleToDelete = $this->getHelper()->createRuleToDelete($customerGroupIds, $country);
        $this->getDeleteUseCase()->deleteRule($ruleToDelete);
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
