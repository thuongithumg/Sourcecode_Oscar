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
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Reportsuccess Helper
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */
class Magestore_Reportsuccess_Helper_Backup extends Mage_Core_Helper_Abstract
{
    /**
     * Backup type constant for database backup
     */
    const TYPE_DB = 'db';

    /**
     * Get all possible backup type values with descriptive title
     *
     * @return array
     */
    public function getBackupTypes()
    {
        return array(
            self::TYPE_DB                     => $this->__('Report Success'),
        );
    }
    /**
     * Get all possible backup type values
     *
     * @return array
     */
    public function getBackupTypesList()
    {
        return array(
            self::TYPE_DB,
        );
    }

    /**
     * Get default backup type value
     *
     * @return string
     */
    public function getDefaultBackupType()
    {
        return self::TYPE_DB;
    }

    /**
     * Get directory path where backups stored
     *
     * @return string
     */
    public function getBackupsDir()
    {
        return Mage::getBaseDir('var') . DS . 'backups' . DS . 'reportsuccess';
    }

    /**
     * Get backup file extension by backup type
     *
     * @param string $type
     * @return string
     */
    public function getExtensionByType($type)
    {
        $extensions = $this->getExtensions();
        return isset($extensions[$type]) ? $extensions[$type] : '';
    }

    /**
     * Get all types to extensions map
     *
     * @return array
     */
    public function getExtensions()
    {
        return array(
            self::TYPE_DB => 'gz'
        );
    }

    /**
     * @return array
     */
    public function multiwarehouse(){
        $warehouses = Mage::getResourceModel('inventorysuccess/warehouse_collection');
        $warehouseIds = array();
        $warehouseIds[1] = 'all';
        foreach($warehouses as $key => $value){
            $warehouseIds[$value['warehouse_id']] = $value['warehouse_code'];
        }
        return $warehouseIds;
    }

    /**
     * Generate backup download name
     *
     * @param Mage_Backup_Model_Backup $backup
     * @return string
     */
    public function generateBackupDownloadName(Magestore_Reportsuccess_Model_Historics $backup)
    {
        $warehouse = $this->multiwarehouse();
        $code = $warehouse[$backup->getName()];
        $additionalExtension = $backup->getType() == self::TYPE_DB ? '.csv' : '';
        return $code . '-' . date('YmdHis', $backup->getTime()) . $additionalExtension . '.'
        . $this->getExtensionByType($backup->getType());
    }
    /**
     * Get backup create success message by backup type
     *
     * @param string $type
     * @return string
     */
    public function getCreateSuccessMessageByType($type)
    {
        $messagesMap = array(
            self::TYPE_DB => $this->__('The Historical Report has been created.')
        );
        if (!isset($messagesMap[$type])) {
            return;
        }
        return $messagesMap[$type];
    }

    /**
     * Invalidate Cache
     * @return Mage_Backup_Helper_Data
     */
    public function invalidateCache()
    {
        if ($cacheTypesNode = Mage::getConfig()->getNode(Mage_Core_Model_Cache::XML_PATH_TYPES)) {
            $cacheTypesList = array_keys($cacheTypesNode->asArray());
            Mage::app()->getCacheInstance()->invalidateType($cacheTypesList);
        }
        return $this;
    }
    /**
     * Invalidate Indexer
     *
     * @return Mage_Backup_Helper_Data
     */
    public function invalidateIndexer()
    {
        foreach (Mage::getResourceModel('index/process_collection') as $process){
            $process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        }
        return $this;
    }

    /**
     * Creates backup's display name from it's name
     *
     * @param string $name
     * @return string
     */
    public function nameToDisplayName($name)
    {
        return str_replace('_', ' ', $name);
    }

    /**
     * Extracts information from backup's filename
     *
     * @param string $filename
     * @return Varien_Object
     */
    public function extractDataFromFilename($filename)
    {
        $extensions = $this->getExtensions();

        $filenameWithoutExtension = $filename;

        foreach ($extensions as $extension) {
            $filenameWithoutExtension = preg_replace('/' . preg_quote($extension, '/') . '$/', '',
                $filenameWithoutExtension
            );
        }
        $filenameWithoutExtension = substr($filenameWithoutExtension, 0, strrpos($filenameWithoutExtension, "."));
        list($time, $type) = explode("_", $filenameWithoutExtension);
        $name = str_replace($time . '_' . $type, '', $filenameWithoutExtension);
        if (!empty($name)) {
            $name = substr($name, 1);
        }
        $result = new Varien_Object();
        $result->addData(array(
            'name' => $name,
            'type' => $type,
            'time' => $time
        ));
        return $result;
    }
}