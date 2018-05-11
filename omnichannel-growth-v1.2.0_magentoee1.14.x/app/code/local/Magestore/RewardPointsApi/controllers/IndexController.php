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
 * @package     Magestore_RewardPointsApi
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * RewardPointsApi Index Controller
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsApi
 * @author      Magestore Developer
 */
class Magestore_RewardPointsApi_IndexController extends Mage_Core_Controller_Front_Action {

    /**
     * index action
     */
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * test XML-RPC
     */
//    public function testAction() {
//        $xml_rpc = Mage::getBaseUrl() . "/api/xmlrpc/";
//        $cli = new Zend_XmlRpc_Client($xml_rpc);
//        $username = 'bach';
//        $password = '123456';
//        $session_id = $cli->call('login', array($username, $password));
//        $method = 'rewardpoints_customer.getcustomerbyemail';
//        $input = 'bach2@mail.com';
//        try {
//            if ($input)
//                $result = $cli->call('call', array($session_id, $method, $input));
//            else {
//                $result = $cli->call('call', array($session_id, $method));
//            }
//        } catch (Exception $exc) {
//            echo $exc->getMessage();
//        }
//
//        if ($result) {
//            echo '<pre>';
//            print_r($result);
//            echo '</pre>';
//        }
//
//        $cli->call('endSession', array($session));
//        Zend_Debug::dump(Mage::getConfig()->getModuleConfig('Magestore_RewardPoints')->is('active','true'));
//        die('22');
//    }
}

