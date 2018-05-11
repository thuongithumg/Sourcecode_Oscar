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
abstract class Magestore_Coresuccess_Block_Adminhtml_Modal_Abstract extends Mage_Adminhtml_Block_Template
{
    /**
     * @var string
     */
    protected $modalId = 'modal_id';

    /**
     * 
     * @return string
     */
    public function getModalId()
    {
        return $this->modalId;
    }
    
    /**
     * Get content
     *
     * @return string
     */
    abstract public function getContent();

    /**
     * Get import title
     *
     * @return string
     */
    abstract public function getTitle();

}
