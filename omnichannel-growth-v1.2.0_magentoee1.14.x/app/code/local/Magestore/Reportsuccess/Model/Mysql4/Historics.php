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
class Magestore_Reportsuccess_Model_Mysql4_Historics extends
    Mage_Core_Model_Mysql4_Abstract
{
    const CRON_STRING_PATH = 'reportsuccess/historics/cron';
    const ID = 'id';
    /**
     * contruct
     */
    public function _construct()
    {
        $this->_init('reportsuccess/historics', self::ID);
    }

    /**
     *  dispatch even from Indexer
     */
    public function reindexAll(){
        $this->createDB(false);
        //$this->service()->prepareDataHistorics();
    }
    /**
     * @return Magestore_Reportsuccess_Model_Service_Inventoryreport_InventoryService
     */
    public function service(){
        return Magestore_Coresuccess_Model_Service::reportInventoryService();
    }
    public function createDB($cron){
        /* check time to run cron */
        if($this->checkCronTime($cron) == false){
            return;
        }
        $helper = Mage::helper('reportsuccess/backup');
        try {
            $type = Magestore_Reportsuccess_Helper_Backup::TYPE_DB;
            $backupManager = $this->getBackupInstance($type)
                ->setBackupExtension($helper->getExtensionByType($type))
                ->setTime(time())
                ->setBackupsDir($helper->getBackupsDir());
            $backupManager->setName('Mr.Kai-at-Magestore');
            Mage::register('backup_manager_ms', $backupManager);
            $successMessage = $helper->getCreateSuccessMessageByType($type);
            $backupManager->create();
            $this->_getSession()->addSuccess($successMessage);
        } catch (Mage_Backup_Exception_NotEnoughFreeSpace $e) {
            $errorMessage = Mage::helper('backup')->__('Not enough free space to create backup.');
        } catch (Mage_Backup_Exception_NotEnoughPermissions $e) {
            Mage::log($e->getMessage());
            $errorMessage = Mage::helper('backup')->__('Not enough permissions to create backup.');
        } catch (Exception  $e) {
            Mage::log($e->getMessage());
            $errorMessage = Mage::helper('backup')->__('An error occurred while creating the backup.');
        }
    }
    public function getBackupInstance($type)
    {
        $class = 'Magestore_Reportsuccess_Model_Service_Inventoryreport_Historics_' . ucfirst($type);
        if (!class_exists($class, true)){
            throw new Mage_Exception('Current implementation not supported this backup.');
        }
        return new $class();
    }
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }
    public function checkCronTime($cron){
        if(!$cron || $cron == false){
            return true;
        }
        $now = Mage::getModel('core/date')->timestamp(time());
        $getTimeNow = date('H', $now);
        $lastUpdate = Mage::getStoreConfig(self::CRON_STRING_PATH);
        if(!$lastUpdate){
            $lastUpdate = 0;
        }
        $time = Mage::getStoreConfig("reportsuccess/general/time_updates");
        if($now >= $lastUpdate && $getTimeNow >= $time){
            /* now + 1days */
            Mage::getModel('core/config')->saveConfig(self::CRON_STRING_PATH, $now + 86400);
            return true;
        }else{
            return false;
        }

    }
}