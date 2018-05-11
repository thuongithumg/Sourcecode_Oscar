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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Class Magestore_Webpos_Model_Api2_Customer_Rest_Admin_V1
 */
class Magestore_Webpos_Model_Api2_Customer_Rest_Admin_V1 extends Magestore_Webpos_Model_Api2_Abstract
{

    /**
     *
     */
    const OPERATION_CREATE_CUSTOMER = 'save';

    /**
     *
     */
    const OPERATION_GET_CUSTOMER_LIST = 'get';

    const OPERATION_LOAD_CUSTOMER = 'load';


    /**
     * Dispatch actions
     */
    public function dispatch()
    {
        $this->_initStore();
        switch ($this->getActionType()) {
            case self::OPERATION_CREATE_CUSTOMER:
                $params = $this->getRequest()->getBodyParams();
                $result = $this->createCustomer($params);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::OPERATION_GET_CUSTOMER_LIST:
                $result = $this->getCustomerList();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::OPERATION_LOAD_CUSTOMER:
                $result = $this->loadCustomer();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
        }
    }

    /**
     * @return mixed
     * @throws Exception
     * @throws Mage_Api2_Exception
     */
    public function getCustomerList()
    {

        $customerCollection = Mage::getResourceModel('customer/customer_collection');
        $customerCollection
            ->addAttributeToSelect('taxvat')
            ->addAttributeToSelect('firstname')
            ->addAttributeToSelect('lastname')
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('default_shipping');
        $customerCollection->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left')
            ->joinAttribute('company', 'customer_address/company', 'default_billing', null, 'left')
            ->addExpressionAttributeToSelect('full_name', 'CONCAT({{firstname}}, " ", {{lastname}})', array('firstname','lastname'));
        ;
//        $websiteId = Mage::app()->getStore()->getWebsiteId();
//        $customerCollection
//            ->getSelect()
//            ->where("e.website_id = '$websiteId'")
//            ->columns('IFNULL(at_telephone.value,"N/A") AS telephone')
//            ->columns('e.entity_id AS id')
//        ;
        $customerCollection
            ->getSelect()
            ->where('website_id != 0')
            ->columns('IFNULL(at_telephone.value,"N/A") AS telephone')
            ->columns('entity_id AS id')
            ;

        $pageNumber = $this->getRequest()->getPageNumber();
        if(!$pageNumber){
            $pageNumber = 1;
        }
        if ($pageNumber != abs($pageNumber)) {
            $this->_critical(self::RESOURCE_COLLECTION_PAGING_ERROR);
        }

        $pageSize = $this->getRequest()->getPageSize();
        if(!$pageSize){
            $pageSize = 20;
        }
        if ($pageSize) {
            if ($pageSize != abs($pageSize) || $pageSize > self::PAGE_SIZE_MAX) {
                $this->_critical(self::RESOURCE_COLLECTION_PAGING_LIMIT_ERROR);
            }
        }


        $orderField = $this->getRequest()->getOrderField();

        if (null !== $orderField) {
            $customerCollection->setOrder($orderField, $this->getRequest()->getOrderDirection());
        }


        /* @var Varien_Data_Collection_Db $customerCollection */
        $this->_applyFilter($customerCollection);
        $this->_applyFilterOr($customerCollection);

        $numberOfCustomer = $customerCollection->getSize();
        $customerCollection->setCurPage($pageNumber)->setPageSize($pageSize);
        $customers = array();
        foreach ($customerCollection as $customerModel) {
            $addressesData = array();
            $customerAddresses = $customerModel->getAddressesCollection()->getItems();
            foreach ($customerAddresses as $address) {
                $address->setData('id', $address->getData('entity_id'));
                if ($address->getData('entity_id') == $customerModel->getData('default_billing')) {
                    $address->setData('default_billing', true);
                } else {
                    $address->setData('default_billing', false);
                }

                if ($address->getData('entity_id') == $customerModel->getData('default_shipping')) {
                    $address->setData('default_shipping', true);
                } else {
                    $address->setData('default_shipping', false);
                }
                $region = $address->getData('region');
                $regionArray = array(
                    'region' => $region,
                    'region_id' => $address->getData('region_id')
                );
                $address->setData('region',$regionArray);
                $street = array(
                    $address->getStreet(1),
                    $address->getStreet(2)
                );
                $address->setData('street', $street);
                $addressesData[] = $address->getData();
            }
            $customerModel->setData('addresses', $addressesData);
            $customers[] = $customerModel->getData();
        }

        if ($pageSize != 0 && $pageNumber <= ($numberOfCustomer/$pageSize+1)) {
            $result['items'] = $customers;
            $result['total_count'] = $numberOfCustomer;
        } else {
            $result = array(
                'items' => array(),
            );
        }

        return $result;

    }


    /**
     * @param bool $customerId
     * @return mixed
     */
    public function loadCustomer($customerId = false)
    {
        $customerId = ($customerId)?$customerId:$this->getRequest()->getParam('customerId');
        $customerModel = Mage::getModel('customer/customer')->load($customerId);
        $addressesData = array();
        $customerAddresses = $customerModel->getAddressesCollection()->getItems();
        foreach ($customerAddresses as $address) {
            $address->setData('id', $address->getData('entity_id'));
            if ($address->getData('entity_id') == $customerModel->getData('default_billing')) {
                $address->setData('default_billing', true);
            } else {
                $address->setData('default_billing', false);
            }

            if ($address->getData('entity_id') == $customerModel->getData('default_shipping')) {
                $address->setData('default_shipping', true);
            } else {
                $address->setData('default_shipping', false);
            }
            $region = $address->getData('region');
            $regionArray = array(
                'region' => $region,
                'region_id' => $address->getData('region_id')
            );
            $address->setData('region',$regionArray);
            $street = array(
                $address->getStreet(1),
                $address->getStreet(2)
            );
            $address->setData('street', $street);
            $addressesData[] = $address->getData();
        }
        $customerModel->setData('addresses', $addressesData);
        $result = $customerModel->getData();
        $result['id'] = $result['entity_id'];
        $result['full_name'] = $result['firstname'].' '.$result['lastname'];
        return $result;

    }

    public function addSubscriber($email) {
        $subscriber = Mage::getModel('newsletter/subscriber');
        $subscriber->setSubscriberEmail($email);
        $subscriber->setStoreId(Mage::app()->getStore()->getId());
        $subscriber->setCustomerId(0);
        $subscriber->setIsStatusChanged(true);
        $subscriber->setStatus(1);
        try {
            $subscriber->save();
        }catch (Exception $e){

        }
    }

    /**
     * @param $params
     */
    public function createCustomer($params) {
        //set default info if it is null
        $pass = Mage::helper('core')->uniqHash();
        $passConfirm = $pass;
        $store = Mage::app()->getStore();
        $session = $this->getRequest()->getParam('session');
        $storeId = Mage::getModel('webpos/user_webpossession')->getStoreIdBySession($session);
        Mage::app()->setCurrentStore($storeId);
        if (isset($params['customer'])) {
            $customerInformation = $params['customer'];
            if ( isset($customerInformation['id']) && !is_numeric($customerInformation['id'])) {
                unset($customerInformation['id']);
                $isSubscriber = $customerInformation['subscriber_status'];
                if ($isSubscriber) {
                    $email = $customerInformation['email'];
                    $this->addSubscriber($email);
                }
                $addresses = $customerInformation['addresses'];
                $customer = Mage::getModel('customer/customer')
                    ->setStoreId(Mage::getModel('core/store')->load($storeId))
                    ->setData($customerInformation)
                    ->setPassword($pass)
                    ->setTaxVat(empty($customerInformation['taxvat'])?'':$customerInformation['taxvat'])
                    ->setConfirmation($passConfirm);
                foreach($addresses as $addressData) {
                    $addressRegion = $addressData['region'];
                    $addressData['region'] = $addressRegion['region'];
                    //$addressData['street'] = implode(',', $addressData['street']);
                    $addressData['is_default_billing'] =  $addressData['default_billing'];
                    $addressData['is_default_shipping'] =  $addressData['default_shipping'];
                    $address   = Mage::getModel('customer/address');
                    $address->addData($addressData);
                    $customer->addAddress($address);
                }
                try {
                    $customer->save();
                    //auto confirm new account if it is needed
                    if ($customer->getConfirmation()) {
                        $customer->setConfirmation(null);
                        $customer->save();
                    }
                    $customer->sendNewAccountEmail('registered', '', $store->getStoreId());
                    Mage::dispatchEvent('customer_register_success', array('customer' => $customer));
                    return $this->loadCustomer($customer->getId());
                } catch (Exception $e) {
                    $result['error'] = true;
                    $result['errmessage'] = $e->getMessage();
                    return $result;
                }
            } else {
                $customer = Mage::getModel('customer/customer')->load($customerInformation['id']);
                $customer->setEmail($customerInformation['email']);
                $customer->setFirstname($customerInformation['firstname']);
                $customer->setFullName($customerInformation['full_name']);
                $customer->setLastname($customerInformation['lastname']);
                $customer->setGroupId($customerInformation['group_id']);
                $customer->setTaxvat($customerInformation['taxvat']);
                $customer->setSubscriberStatus($customerInformation['subscriber_status']);
                $postAddress = $customerInformation['addresses'];
                if ($postAddress !== null && count($postAddress)) {
                    $existingAddresses = $customer->getAddresses();
                    $getIdFunc = function ($address) {
                        return $address->getId();
                    };
                    $existingAddressIds = array_map($getIdFunc, $existingAddresses);

                    $postAddressIds = array();
                    foreach ($postAddress as $address) {

                        $postAddressIds[] = $address['entity_id'];
                        $address['customer_id'] = $customerInformation['id'];
                        $address['region'] = $address['region']['region'];
                        //$addressModel = Mage::getModel('customer/address')->load($address['entity_id']);

                        if (!in_array($address['entity_id'], $existingAddressIds)) {
                            if (isset($address['id'])) {
                                unset($address['id']);
                            }
                            if (isset($address['entity_id'])) {
                                unset($address['entity_id']);
                            }
                            $addressModel = Mage::getModel('customer/address');

                            $addressModel->addData($address);
                            if (isset($address['default_billing']) && $address['default_billing']) {
                                $addressModel->setIsDefaultBilling(true);
                            } else {
                                $addressModel->setIsDefaultBilling(false);
                            }
                            if (isset($address['default_shipping']) && $address['default_shipping']) {
                                $addressModel->setIsDefaultShipping(true);
                            } else {
                                $addressModel->setIsDefaultShipping(false);
                            }
                            $customer->addAddress($addressModel);
                        } else {

                            foreach ($existingAddresses as $editAddress) {
                                if ($editAddress->getData('entity_id') == $address['entity_id']) {
                                    $data = $address;
                                    $editAddress->setData($data);
                                    if (isset($address['default_billing']) && ($address['default_billing'] == true)) {
                                        $editAddress->setIsDefaultBilling(true);
                                    } else {
                                        $editAddress->setIsDefaultBilling(false);
                                    }
                                    if (isset($address['default_shipping']) && ($address['default_shipping'] == true)) {
                                        $editAddress->setIsDefaultShipping(true);
                                    } else {
                                        $editAddress->setIsDefaultShipping(false);
                                    }

                                }
                            }
                        }

                    }

                    $addressIdsToDelete = array_diff($existingAddressIds, $postAddressIds);
                    foreach ($existingAddresses as $address) {
                        if (in_array($address->getId(), $addressIdsToDelete)) {
                            $address->setData('_deleted', true);
                        }
                    }
                }
                $customer->save();
                return $this->loadCustomer($customer->getId());
            }
        }
    }
}
