/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
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