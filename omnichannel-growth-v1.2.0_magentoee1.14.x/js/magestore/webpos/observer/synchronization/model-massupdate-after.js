/*
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

define(
    [
        'jquery',
        'eventManager'
    ],
    function ($, eventManager) {
        "use strict";

        return {
            execute: function() {
                eventManager.observer('model_massupdate_after',function(event, eventData){
                    /* check mode */
                    var model = eventData.model;
                    var items = eventData.items
                    if(model.getMode() == 'online') {
                        return;
                    }
                    /* auto push is false */
                    if(!model.push) {
                        return;
                    }
                    /* has no online resource */
                    if(!model.getResourceOnline()){
                        return;
                    }
                    /* auto push to server */
                    model.getResourceOnline().massUpdate(items);
                });
            }
        }        
    }
);