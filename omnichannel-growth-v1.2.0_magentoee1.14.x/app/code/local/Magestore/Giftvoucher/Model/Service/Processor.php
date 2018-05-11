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
 * @package     Magestore_Giftvoucher
 * @module      Giftvoucher
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */


/**
 * Class Magestore_Giftvoucher_Model_Service_Processor
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Magestore_Giftvoucher_Model_Service_Processor extends Mage_Core_Model_Abstract
{

    protected $_templateFilter;

    /**
     * @param $giftCode
     * @param $giftTemplate
     * @return mixed
     */
    public function preview($giftCode, $giftTemplate)
    {
        $variables = [];
        $storeId = 0;
        if (is_null($giftCode) || !$giftCode->getId()) {
            $variables = Mage::helper('giftvoucher/template')->getSampleData();
            $variables[Magestore_Giftvoucher_Model_GiftTemplate::IMAGE_URL_PRINT] = Mage::helper('giftvoucher/template')->getFirstImageUrl($giftTemplate);
            $variables[Magestore_Giftvoucher_Model_GiftTemplate::BACKGROUND_URL_PRINT] = Mage::helper('giftvoucher/template')->getBackgroundImageUrl($giftTemplate);
        } else {
            $variables = Mage::helper('giftvoucher/template')->toPrintData($giftCode, $giftTemplate);
            $storeId = $giftCode->getStoreId();
        }
        /* insert data of gift template */
        $variables[Magestore_Giftvoucher_Model_GiftTemplate::TEXT_COLOR_PRINT] = $this->getTemplateTextColor($giftTemplate);
        $variables[Magestore_Giftvoucher_Model_GiftTemplate::STYLE_COLOR_PRINT] = $this->getTemplateStyleColor($giftTemplate);
        $variables[Magestore_Giftvoucher_Model_GiftTemplate::TITLE_PRINT] = $this->getTemplateTitle($giftTemplate);
        $variables[Magestore_Giftvoucher_Model_GiftTemplate::NOTES_PRINT] = $this->getProcessedNotes($giftTemplate->getNotes(), $storeId);

        return $this->getProcessedTemplate($variables, $giftTemplate->getDesignPattern());
    }

    /**
     * Process gift template to HTML
     *
     * @param array $variables
     * @param string $giftTemplateId
     * @return string
     */
    public function getProcessedTemplate(array $variables, $giftTemplateId)
    {
        $processor = $this->getTemplateFilter();
        $variables['this'] = $this;
        $processor->setVariables($variables);
        $processedResult = $processor->filter($this->getTemplateContent($giftTemplateId));
        return $processedResult;
    }

    /**
     * Get filter object for template processing logi
     *
     * @return Mage_Core_Model_Email_Template_Filter
     */
    public function getTemplateFilter()
    {
        if (empty($this->_templateFilter)) {
            $this->_templateFilter = Mage::getModel('core/email_template_filter');
        }
        return $this->_templateFilter;
    }

    /**
     *
     * @param string $giftTemplateId
     * @return string
     */
    protected function getTemplateContent($giftTemplateId)
    {
        return $this->preProcessTemplate(Mage::helper('giftvoucher/template')->getTemplateContent($giftTemplateId));
    }

    /**
     *
     * @param string $content
     * @return string
     */
    protected function preProcessTemplate($content)
    {
        /* @TODO: remove HTML comments */
        return $content;
    }

    /**
 * Get text color code of gifttemplate
 *
 * @param \Magestore\Giftvoucher\Api\Data\GiftTemplateInterface $giftTemplate
 * @return string
 */
    public function getTemplateTextColor($giftTemplate)
    {
        return str_replace('#', '', $giftTemplate->getTextColor());
    }

    /**
     * Get text color code of gifttemplate
     *
     * @param \Magestore\Giftvoucher\Api\Data\GiftTemplateInterface $giftTemplate
     * @return string
     */
    public function getTemplateTitle($giftTemplate)
    {
        return str_replace('#', '', $giftTemplate->getCaption());
    }

    /**
     * Get style color code of gifttemplate
     *
     * @param \Magestore\Giftvoucher\Api\Data\GiftTemplateInterface $giftTemplate
     * @return string
     */
    public function getTemplateStyleColor($giftTemplate)
    {
        return str_replace('#', '', $giftTemplate->getStyleColor());
    }

    /**
     *
     * @param string $note
     * @param int $storeId
     * @return string
     */
    protected function getProcessedNotes($note, $storeId = 0)
    {
        $store = Mage::app()->getStore($storeId);
        $storeName = $store->getFrontendName();
        $storeUrl = $store->getBaseUrl();
        $storeAddress = Mage::getStoreConfig('general/store_information/address', $storeId);

        $note = str_replace('{store_name}', $storeName, $note);
        $note = str_replace('{store_url}', $storeUrl, $note);
        $note = str_replace('{store_address}', $storeAddress, $note);
        return $note;
    }

    /**
     * Print-out gift code to a gift card HTML
     *
     * @param $giftCode
     * @return mixed
     */
    public function printGiftCodeHtml($giftCode)
    {
        $giftTemplate = Mage::getModel('giftvoucher/gifttemplate')->load($giftCode->getGiftcardTemplateId());
        return $this->preview($giftCode, $giftTemplate);
    }
}
