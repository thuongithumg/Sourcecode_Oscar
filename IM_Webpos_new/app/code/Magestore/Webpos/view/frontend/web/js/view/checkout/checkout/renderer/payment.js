/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/base/grid/renderer/abstract',
    ],
    function ($, ko, renderAbstract) {
        "use strict";
        return renderAbstract.extend({
            render: function (item) {
                var posPayments = [
                    'cashforpos',
                    'codforpos',
                    'ccforpos',
                    'cp1forpos',
                    'cp2forpos'
                ];
                var code = item.code;
                if(item.type == '1'){
                    code = 'ccforpos';
                }else{
                    if($.inArray(item.code, posPayments) < 0){
                        code = 'cp1forpos';
                    }
                }
                return 'icon-iconPOS-payment-' + code;
            }
        });
    }
);