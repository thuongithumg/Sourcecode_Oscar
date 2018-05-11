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
 * Reportsuccess Magestore_Reportsuccess_Model_Service_Inventoryreport_Historics_Db
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */
class Magestore_Reportsuccess_Model_Service_Inventoryreport_Historics_Db extends Mage_Backup_Abstract
{
    /**
     * Implements Rollback functionality for Db
     *
     * @return bool
     */
    public function rollback()
    {
        set_time_limit(0);
        ignore_user_abort(true);

        $this->_lastOperationSucceed = false;

        $archiveManager = new Mage_Archive();
        $source = $archiveManager->unpack($this->getBackupPath(), $this->getBackupsDir());

        $file = new Mage_Backup_Filesystem_Iterator_File($source);
        foreach ($file as $statement) {
            $this->getResourceModel()->runCommand($statement);
        }
        @unlink($source);

        $this->_lastOperationSucceed = true;

        return true;
    }


    /**
     * @return bool
     */
    public function create()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $this->_lastOperationSucceed = false;
        $backupDb = Mage::getSingleton('reportsuccess/service_inventoryreport_historics_createdb');
        $time = $this->getTime();
        $multi_warehouse = Mage::helper('reportsuccess/backup')->multiwarehouse();
        foreach($multi_warehouse as $wid => $name){
            $time++;
            $backup = Mage::getModel('backup/backup')
                ->setTime($time)
                ->setType($this->getType())
                ->setPath($this->getBackupsDir())
                ->setName($wid);
            $backupDb->createBackup($backup, $wid, $name);
        }
        Mage::getSingleton('reportsuccess/service_cron_reportindexer')
            ->stop(Magestore_Reportsuccess_Model_Service_Cron_Reportindexer::CACHE_TYPE_HISTORICS_REPORT);
        $this->_lastOperationSucceed = true;
        return true;
    }
    /**
     * Get Backup Type
     *
     * @return string
     */
    public function getType()
    {
        return 'db';
    }

}
