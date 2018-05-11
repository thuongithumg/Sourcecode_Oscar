<?php
/**
 * Created by PhpStorm.
 * User: duongdiep
 * Date: 02/04/2017
 * Time: 21:40
 */


class Magestore_Debugsuccess_Model_Mysql4_Stockmovement extends
    Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('debugsuccess/stockmovement', Magestore_Debugsuccess_Model_Stockmovement::ID);
    }
}