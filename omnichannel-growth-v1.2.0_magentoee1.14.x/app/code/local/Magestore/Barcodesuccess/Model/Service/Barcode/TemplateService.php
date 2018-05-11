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
 *   @package     Magestore_Barcodesuccess
 *   @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 *   @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Class Magestore_Barcodesuccess_Model_Service_Barcode_TemplateService
 */
class Magestore_Barcodesuccess_Model_Service_Barcode_TemplateService
{
    /**
     * @return array
     */
    public function getTemplateOptionArray()
    {
        $activeTemplates = $this->getActiveTemplates();
        $options         = array();
        /** @var Magestore_Barcodesuccess_Model_Template $template */
        $first_option = Mage::getStoreConfig('barcodesuccess/general/default_barcode_template');

        if($first_option){
            $options[$first_option] = $this->getDefaulOptions($first_option);
        }
        foreach ( $activeTemplates as $template ) {
            if($first_option && $first_option == $template->getId())
                continue;
            $options[$template->getId()] = $template->getName();
        }
        return $options;
    }

    /**
     * get all active templates
     * @return Magestore_Barcodesuccess_Model_Mysql4_Template_Collection
     */
    public function getActiveTemplates()
    {
        $templateCollection = Mage::getModel('barcodesuccess/template')->getCollection()
                                  ->addFieldToFilter(Magestore_Barcodesuccess_Model_Template::STATUS, Magestore_Barcodesuccess_Model_Source_Template_Status::ACTIVE);
        return $templateCollection;
    }

    /**
     * get default option
     * @return Magestore_Barcodesuccess_Model_Mysql4_Template_Collection
     */
    public function getDefaulOptions($first_option)
    {
        $name = $this->getActiveTemplates()->addFieldToFilter('template_id',$first_option)->getColumnValues('name');
        if($name)
        return $name[0];
        return false;
    }

    /**
     * @param $barcodes
     * @param $template
     */
    public function getHtml(
        $barcodes,
        $template
    ) {

    }
}