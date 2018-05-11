<?php
/**
 * Created by PhpStorm.
 * User: duongdiep
 * Date: 02/04/2017
 * Time: 14:32
 */


class Magestore_Debugsuccess_Helper_Data extends
    Mage_Core_Helper_Abstract
{
    const WRONG_QTY = 'wrong_qty';
    const ON_HOLD_QTY = 'on_hold_qty';

    /**
     *
     * @param string $data
     * @return string
     */
    public function base64Decode($data, $strict = false) {
        return base64_decode($data, $strict);
    }
    /**
     *
     * @param string $data
     * @return string
     */
    public function base64Encode($data) {
        return base64_encode($data);
    }

}