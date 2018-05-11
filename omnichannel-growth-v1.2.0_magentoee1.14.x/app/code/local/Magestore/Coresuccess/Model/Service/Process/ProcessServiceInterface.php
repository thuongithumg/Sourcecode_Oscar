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
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Coresuccess Status Model
 * 
 * @category    Magestore
 * @package     Magestore_Coresuccess
 * @author      Magestore Developer
 */
interface Magestore_Coresuccess_Model_Service_Process_ProcessServiceInterface
{
    /**
     * get steps in process
     * 
     * @return array()
     */
    public function getSteps();
    
    /**
     * do process by step
     */
    public function processByStep();
}