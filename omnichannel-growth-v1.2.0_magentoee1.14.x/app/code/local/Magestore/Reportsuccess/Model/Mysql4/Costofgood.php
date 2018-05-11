<?php
/**
 *
 *  Magestore
 *   NOTICE OF LICENSE
 *
 *   This source file is subject to the Magestore.com license that is
 *   available through the world-wide-web at this URL:
 *   http://www.magestore.com/license-agreement.html
 *
 *   DISCLAIMER
 *
 *   Do not edit or add to this file if you wish to upgrade this extension to newer
 *   version in the future.
 *
 *   @category    Magestore
 *   @package     Magestore_Reportsuccess
 *   @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 *   @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * ReportSuccess Resource Model
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */
class Magestore_Reportsuccess_Model_Mysql4_Costofgood extends
    Mage_Core_Model_Mysql4_Abstract
{
    /**
     *
     */
    public function _construct()
    {
        $this->_init('reportsuccess/costofgood', Magestore_Reportsuccess_Model_Costofgood::ID);
    }

    /**
     * @param $data
     * @return bool
     */
    public function _updateMacData($data){
            $insert = $data['insert'];
            $table = $data['table'];
            $bin_data = $data['values'];
            $where = $data['condition'];
            try{
                $connection = $this->_getConnection('read');
                $connection->beginTransaction();
                if($bin_data) {
                    $connection->update($table, $bin_data, $where);
                }
                if($insert) {
                    $connection->insertMultiple($table, $insert);
                }
                $connection->commit();
                return true;
            }catch (Exception $e){
                $connection->rollBack();
                Mage::log($e->getMessage(),null,'log.log');
            }
    }
}