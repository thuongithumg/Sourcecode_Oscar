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
 * @package     Magestore_Purchaseordersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Purchaseorder Service
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Item as PurchaseorderItem;

class Magestore_Purchaseordersuccess_Model_Service_Purchaseorder_CodeService
    extends Magestore_Purchaseordersuccess_Model_Service_AbstractService
{
    const DEFAULT_ID = 1;
    const CODE_LENGTH = 8;
    
    /**
     * Generate purchase order code
     *
     * @param string $prefix
     * @return string $code
     */
    public static function generateCode($prefix = ''){
        $nextId = self::getNextId($prefix);

        /* generate the increment id */
        $formatId = pow(10, self::CODE_LENGTH + 1) + $nextId;
        $formatId = (string) $formatId;
        $formatId = substr($formatId, 0-self::CODE_LENGTH);

        return $prefix . $formatId;
    }

    /**
     * Get next increment Id
     *
     * @param string $prefix
     * @return int
     */
    public static function getNextId($prefix)
    {
        /** @var Magestore_Purchaseordersuccess_Model_Purchaseorder_Code $model */
        $model = Mage::getModel('purchaseordersuccess/purchaseorder_code')
            ->load($prefix, 'code');
        $nextId = $model->getCurrentId() + 1;
        $model->setCurrentId($nextId);
        if (!$model->getId()) {
            $model->setCode($prefix);
            $model->setId(null);
        }
        $model->save();
        return $nextId;
    }
}