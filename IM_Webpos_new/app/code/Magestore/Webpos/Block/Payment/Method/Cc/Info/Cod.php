<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Block\Payment\Method\Cc\Info;

/**
 * class \Magestore\Webpos\Block\Payment\Method\Cc\Info\Cod
 * 
 * COD for POS info block
 * Methods:
 *  _construct
 *  _prepareSpecificInformation
 *  getMethodTitle
 * 
 * @category    Magestore
 * @package     Magestore\Webpos\Block\Payment\Method\Cc\Info
 * @module      Webpos
 * @author      Magestore Developer
 */
class Cod extends \Magestore\Webpos\Block\Payment\Method\InfoAbstract
{
    /**
     * Get method title from setting
     */
    public function getMethodTitle()
    {
        return $this->_helperPayment->getCodMethodTitle();
    }

}