<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Payment\Method\Cc;

/**
 * class \Magestore\Webpos\Block\Payment\Method\Cc\Ccforpos
 * 
 * CC for POS form block
 * Methods:
 *  _construct
 * 
 * @category    Magestore
 * @package     Magestore\Webpos\Block\Payment\Method\Cc
 * @module      Webpos
 * @author      Magestore Developer
 */
class Ccforpos extends \Magento\Payment\Block\Form
{
    
    /**
     * Construct function
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Magestore_Webpos::payment/method/form/ccforpos.phtml');
    }
    
}