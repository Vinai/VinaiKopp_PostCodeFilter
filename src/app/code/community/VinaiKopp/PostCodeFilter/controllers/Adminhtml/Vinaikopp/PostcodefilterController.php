<?php

use VinaiKopp\PostCodeFilter\Command\RuleToAdd;
use VinaiKopp\PostCodeFilter\Command\RuleToDelete;
use VinaiKopp\PostCodeFilter\UseCases\AdminAddsRule;
use VinaiKopp\PostCodeFilter\UseCases\AdminDeletesRule;
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
            $country = $this->getSubmittedCountry();
            $customerGroupIds = $this->getSubmittedCustomerGroupIds();
            $postCodes = $this->getSubmittedPostCodes();
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
            $country = $this->getRequest()->getParam('country');;
            $customerGroupIds = $this->getCustomerGroupIdsToEdit();
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
            $newCountry = $this->getSubmittedCountry();
            $newCustomerGroupIds = $this->getSubmittedCustomerGroupIds();
            $postCodes = $this->getSubmittedPostCodes();
            
            $this->deleteRule($this->getOldCustomerGroupIds(), $this->getOldCountry());
            $this->addRule($newCustomerGroupIds, $newCountry, $postCodes);
            
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
            $this->deleteRule($this->getOldCustomerGroupIds(), $this->getOldCountry());
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
            $ruleWriter = $this->getHelper()->getRuleWriter();
            $ruleReader = $this->getHelper()->getRuleWriter();
            $this->addRuleUseCase = new AdminAddsRule($ruleWriter, $ruleReader);
        }
        return $this->addRuleUseCase;
    }

    public function setDeleteUseCase(AdminDeletesRule $deleteUseCase)
    {
        $this->deleteRuleUseCase = $deleteUseCase;
    }

    private function getDeleteUseCase()
    {
        if (! $this->deleteRuleUseCase) {
            $ruleWriter = $this->getHelper()->getRuleWriter();
            $ruleReader = $this->getHelper()->getRuleWriter();
            $this->deleteRuleUseCase = new AdminDeletesRule($ruleWriter, $ruleReader);
        }
        return $this->deleteRuleUseCase;
    }

    public function setViewSingleRuleUseCase(AdminViewsSingleRule $viewsRuleUseCase)
    {
        $this->viewsRuleUseCase = $viewsRuleUseCase;
    }

    private function getViewSingleRuleUseCase()
    {
        if (! $this->viewsRuleUseCase) {
            $ruleReader = $this->getHelper()->getRuleReader();
            $this->viewsRuleUseCase = new AdminViewsSingleRule($ruleReader);
        }
        return $this->viewsRuleUseCase;
    }

    /**
     * @return string[]
     */
    private function getSubmittedPostCodes()
    {
        return preg_split("/ *[\n,] */", $this->getRequest()->getPost('post_codes'), null, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * @return mixed
     */
    private function getSubmittedCustomerGroupIds()
    {
        return $this->getRequest()->getPost('customer_group_ids');
    }

    /**
     * @return mixed
     */
    private function getSubmittedCountry()
    {
        return $this->getRequest()->getPost('country');
    }

    /**
     * @param int[] $customerGroupIds
     * @param string $country
     * @param string[]|int[] $postCodes
     */
    private function addRule(array $customerGroupIds, $country, array $postCodes)
    {
        $ruleToAdd = $this->createRuleToAdd($customerGroupIds, $country, $postCodes);
        $this->getAddUseCase()->addRule($ruleToAdd);
    }

    /**
     * @param int[] $customerGroupIds
     * @param string $country
     * @param string[]|int[] $postCodes
     * @return RuleToAdd
     */
    private function createRuleToAdd(array $customerGroupIds, $country, $postCodes)
    {
        return $this->getHelper()->createRuleToAdd($customerGroupIds, $country, $postCodes);
    }

    /**
     * @param int[] $customerGroupIds
     * @param string $country
     */
    private function deleteRule(array $customerGroupIds, $country)
    {
        $ruleToDelete = $this->createRuleToDelete($customerGroupIds, $country);
        $this->getDeleteUseCase()->deleteRule($ruleToDelete);
    }

    /**
     * @param int[] $customerGroupIds
     * @param string $country
     * @return RuleToDelete
     */
    private function createRuleToDelete($customerGroupIds, $country)
    {
        return $this->getHelper()->createRuleToDelete($customerGroupIds, $country);
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
        $groupIds = $this->getRequest()->getParam('old_customer_group_ids');
        return preg_split('/,/', $groupIds, null, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * @return string[]
     */
    private function getCustomerGroupIdsToEdit()
    {
        $groupIds = $this->getRequest()->getParam('customer_group_ids');
        return preg_split('/,/', $groupIds, null, PREG_SPLIT_NO_EMPTY);
    }
}
