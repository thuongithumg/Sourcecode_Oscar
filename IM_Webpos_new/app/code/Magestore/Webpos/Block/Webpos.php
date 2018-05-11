<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block;
class Webpos extends \Magestore\Webpos\Block\AbstractBlock
{
    /**
     * @return string
     */
    public function toHtml()
    {
        $isLogin = $this->_permissionHelper->getCurrentUser();
        if ($isLogin && !$this->_permissionHelper->isShowChoosePosLocation()) {
            return parent::toHtml();
        } else {
            return '';
        }
        
    }
}
