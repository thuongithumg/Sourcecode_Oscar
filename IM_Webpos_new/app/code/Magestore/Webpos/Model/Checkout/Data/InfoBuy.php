<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Checkout\Data;

/**
 * 
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InfoBuy extends \Magento\Framework\Model\AbstractExtensibleModel implements \Magestore\Webpos\Api\Data\Checkout\InfoBuyInterface
{
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getId(){
        return $this->getData(self::KEY_ID);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setId($id){
        return $this->setData(self::KEY_ID,$id);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getChildId(){
        return $this->getData(self::KEY_CHILD_ID);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setChildId($childId){
        return $this->setData(self::KEY_CHILD_ID,$childId);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getQty(){
        return $this->getData(self::KEY_QTY);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setQty($qty){
        return $this->setData(self::KEY_QTY,$qty);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setSuperAttribute($super_attribute){
        return $this->setData(self::KEY_SUPER_ATTRIBUTE,$super_attribute);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getSuperAttribute(){
        $result = null;
        $data = $this->getData(self::KEY_SUPER_ATTRIBUTE);
        if($data){
            foreach($data as $key => $option){
                if($option instanceof \Magestore\Webpos\Model\Checkout\Data\CartItemOption){
                    $result[$option->getCode()] = $option->getValue();
                }else{
                    $result[$key] = $option;
                }
            }
        }
        return $result;
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setSuperGroup($super_group){
        return $this->setData(self::KEY_SUPER_GROUP,$super_group);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getSuperGroup(){
        $result = null;
        $data = $this->getData(self::KEY_SUPER_GROUP);
        if($data){
            foreach($data as $key => $option){
                if($option instanceof \Magestore\Webpos\Model\Checkout\Data\CartItemOption){
                    $result[$option->getCode()] = $option->getValue();
                }else{
                    $result[$key] = $option;
                }
            }
        }
        return $result;
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setOptions($options){
        return $this->setData(self::KEY_CUSTOM_OPTION,$options);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getOptions(){
        $result = null;
        $data = $this->getData(self::KEY_CUSTOM_OPTION);
        if($data){
            foreach($data as $key => $option){
                if($option instanceof \Magestore\Webpos\Model\Checkout\Data\CartItemOption){
                    $result[$option->getCode()] = $option->getValue();
                }else{
                    $result[$key] = $option;
                }
            }
        }
        return $result;
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setBundleOption($bundle_option){
        return $this->setData(self::KEY_BUNDLE_OPTION,$bundle_option);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getBundleOption(){
        $result = null;
        $data = $this->getData(self::KEY_BUNDLE_OPTION);
        if($data){
            foreach($data as $key => $option){
                if($option instanceof \Magestore\Webpos\Model\Checkout\Data\CartItemOption){
                    $result[$option->getCode()] = $option->getValue();
                }else{
                    $result[$key] = $option;
                }
            }
        }
        return $result;
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setBundleOptionQty($bundle_option_qty){
        return $this->setData(self::KEY_BUNDLE_OPTION_QTY,$bundle_option_qty);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getBundleOptionQty(){
        $result = null;
        $data = $this->getData(self::KEY_BUNDLE_OPTION_QTY);
        if($data){
            foreach($data as $key => $option){
                if($option instanceof \Magestore\Webpos\Model\Checkout\Data\CartItemOption){
                    $result[$option->getCode()] = $option->getValue();
                }else{
                    $result[$key] = $option;
                }
            }
        }
        return $result;
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getUnitPrice(){
        return $this->getData(self::KEY_UNIT_PRICE);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setUnitPrice($unitPrice){
        return $this->setData(self::KEY_UNIT_PRICE,$unitPrice);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getBaseUnitPrice(){
        return $this->getData(self::KEY_BASE_UNIT_PRICE);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setBaseUnitPrice($baseUnitPrice){
        return $this->setData(self::KEY_BASE_UNIT_PRICE,$baseUnitPrice);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getOriginalPrice(){
        return $this->getData(self::KEY_ORIGINAL_PRICE);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setOriginalPrice($originalPrice){
        return $this->setData(self::KEY_ORIGINAL_PRICE,$originalPrice);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getBaseOriginalPrice(){
        return $this->getData(self::KEY_BASE_ORIGINAL_PRICE);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setBaseOriginalPrice($baseOriginalPrice){
        return $this->setData(self::KEY_BASE_ORIGINAL_PRICE,$baseOriginalPrice);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getOptionsLabel(){
        return $this->getData(self::KEY_OPTIONS_LABEL);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setOptionsLabel($optionsLabel){
        return $this->setData(self::KEY_OPTIONS_LABEL,$optionsLabel);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getCustomSalesInfo() {
        return $this->getData(self::KEY_CUSTOM_SALES);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setCustomSalesInfo($customSalesInfo){
        return $this->setData(self::KEY_CUSTOM_SALES,$customSalesInfo);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getHasCustomPrice() {
        return $this->getData(self::KEY_HAS_CUSTOM_PRICE);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setHasCustomPrice($isCustomPrice){
        return $this->setData(self::KEY_HAS_CUSTOM_PRICE,$isCustomPrice);
    }
}