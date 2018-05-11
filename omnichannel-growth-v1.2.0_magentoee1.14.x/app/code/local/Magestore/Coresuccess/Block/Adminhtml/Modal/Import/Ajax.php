<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Coresuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
abstract class Magestore_Coresuccess_Block_Adminhtml_Modal_Import_Ajax extends Magestore_Coresuccess_Block_Adminhtml_Modal_Import
{
    /*
     * 
     */
    protected function _construct()
    {
        parent::_construct();        
        $this->setTemplate('coresuccess/modal/import/ajax.phtml');
        $this->_prepareNotices();
    }

    
    /**
     * get id of left tab container
     * 
     * @return string|nulll
     */
    public function getTabContainerId()
    {
        return null;
    }
    
    /**
     * get id of tab which contains product grid
     * 
     * @return string|nulll
     */
    public function getTabId()
    {
        return null;
    }
    
    /**
     * prepare notification messages
     * 
     */
    protected function _prepareNotices()
    {
        $notices = array(
            'invalid_file_message' => $this->__('Invalid file type!'),
            'choose_file_upload_message' => $this->__('Please choose CSV file to import.'),
            'importing_error_message' => $this->__('There was an error while importing.'),
            'upload_failed_message' => $this->__('There was an error attempting to upload the file.'),
            'upload_canceled_message' => $this->__('The upload has been canceled by the user or the browser dropped the connection.'),
        );
        $this->addData($notices);
    }

}