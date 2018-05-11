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

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


define(
    [
    ],
    function(){
         "use strict";
        return {
            getSingleton: function(key, Class){
                if(!window.webposObjects) {
                    window.webposObjects = {};
                } 
                if(!window.webposObjects[key]) {
                     window.webposObjects[key] = Class();
                }

                return window.webposObjects[key];                
            },
            
            createObject: function(Class){
                return Class();
            }
        }
    }
);