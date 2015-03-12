<?php

use VinaiKopp\PostCodeFilter\RuleStorage;

class VinaiKopp_PostCodeFilter_Model_Resource_RuleStorage implements RuleStorage 
{
    /**
     * @var Varien_Db_Adapter_Interface
     */
    private $readConnection;
    
    /**
     * @var Varien_Db_Adapter_Interface
     */
    private $writeConnection;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @param Varien_Db_Adapter_Interface $readConnection
     */
    public function __construct($readConnection = null, $writeConnection = null)
    {
        if (empty($readConnection)) {
            // @codeCoverageIgnoreStart
            $readConnection = Mage::getSingleton('core/resource')->getConnection('default_read');
        }
        // @codeCoverageIgnoreEnd
        if (empty($writeConnection)) {
            // @codeCoverageIgnoreStart
            $writeConnection = Mage::getSingleton('core/resource')->getConnection('default_write');
        }
        // @codeCoverageIgnoreEnd
        $this->readConnection = $readConnection;
        $this->writeConnection = $writeConnection;
        $this->tableName = Mage::getSingleton('core/resource')->getTableName('vinaikopp_postcodefilter/rule');
    }

    /**
     * @param string $iso2country
     * @param int $customerGroupId
     * @return string[]
     */
    public function findPostCodesByCountryAndGroupId($iso2country, $customerGroupId)
    {
        $query = $this->readConnection->select()
            ->from($this->tableName, 'post_codes')
            ->where('country=?', $iso2country)
            ->where('customer_group_id=?', $customerGroupId);
        $result = $this->readConnection->fetchOne($query);
        if (! $result) {
            return [];
        }
        return $this->splitPostCodes($result);
    }

    /**
     * @param string $iso2country
     * @param int[] $customerGroupIds
     * @return mixed[]
     */
    public function findRulesByCountryAndGroupIds($iso2country, array $customerGroupIds)
    {
        $query = $this->readConnection->select()
            ->from($this->tableName)
            ->where('country=?', $iso2country)
            ->where('customer_group_id IN(?)', $customerGroupIds);
        return array_map([$this, 'processResultRecord'], $this->readConnection->fetchAll($query));
    }

    /**
     * @return mixed[]
     */
    public function findAllRules()
    {
        $query = $this->readConnection->select()->from($this->tableName);
        return array_map([$this, 'processResultRecord'], $this->readConnection->fetchAll($query));
    }

    private function processResultRecord($row)
    {
        $row['post_codes'] = $this->splitPostCodes($row['post_codes']);
        $row['customer_group_id'] = (int) $row['customer_group_id'];
        return $row;
    }

    /**
     * @param string $postCodesAsString
     * @return string[]
     */
    private function splitPostCodes($postCodesAsString)
    {
        return preg_split('/ *[,\n\r] */', $postCodesAsString, null, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * @param string $iso2country
     * @param int $customerGroupId
     * @param string[]|int[] $postCodes
     */
    public function create($iso2country, $customerGroupId, array $postCodes)
    {
        sort($postCodes);
        $this->writeConnection->insert(
            $this->tableName,
            [
                'country' => $iso2country,
                'customer_group_id' => $customerGroupId,
                'post_codes' => implode(',', $postCodes)
            ]
        );
    }

    /**
     * @param string $iso2country
     * @param int $customerGroupId
     */
    public function delete($iso2country, $customerGroupId)
    {
        $this->writeConnection->delete(
            $this->tableName,
            [
                'country=?' => $iso2country,
                'customer_group_id=?' => $customerGroupId
            ]
        );
    }

    public function beginTransaction()
    {
        $this->writeConnection->beginTransaction();
    }

    public function commitTransaction()
    {
        $this->writeConnection->commit();
    }

    public function rollbackTransaction()
    {
        $this->writeConnection->rollBack();
    }
}
