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
 * Reportsuccess Resource Collection Model
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */
class Magestore_Reportsuccess_Model_Fs_Collection extends
    Varien_Data_Collection_Filesystem
{
    /**
     * Folder, where all backups are stored
     *
     * @var string
     */
    protected $_baseDir;

    /**
     * Set collection specific parameters and make sure backups folder will exist
     */
    public function __construct()
    {
        parent::__construct();

        $this->_baseDir = Mage::getBaseDir('var') . DS . 'backups'. DS . 'reportsuccess';

        // check for valid base dir
        $ioProxy = new Varien_Io_File();
        $ioProxy->mkdir($this->_baseDir);
        if (!is_file($this->_baseDir . DS . '.htaccess')) {
            $ioProxy->open(array('path' => $this->_baseDir));
            $ioProxy->write('.htaccess', 'deny from all', 0644);
        }

        // set collection specific params
        $extensions = Mage::helper('reportsuccess/backup')->getExtensions();

        foreach ($extensions as $key => $value) {
            $extensions[] = '(' . preg_quote($value, '/') . ')';
        }
        $extensions = implode('|', $extensions);

        $this
            ->setOrder('time', self::SORT_ORDER_DESC)
            ->addTargetDir($this->_baseDir)
            ->setFilesFilter('/^[a-z0-9\-\_]+\.' . $extensions . '$/')
            ->setCollectRecursively(false)
        ;
    }

    /**
     * Get backup-specific data from model for each row
     *
     * @param string $filename
     * @return array
     */
    protected function _generateRow($filename)
    {
        $row = parent::_generateRow($filename);
        foreach (Mage::getSingleton('reportsuccess/historics')->load($row['basename'], $this->_baseDir)
                     ->getData() as $key => $value) {
            $row[$key] = $value;
        }
        $row['size'] = filesize($filename);
        $row['id'] = $row['time'] . '_' . $row['type'];
        return $row;
    }

}