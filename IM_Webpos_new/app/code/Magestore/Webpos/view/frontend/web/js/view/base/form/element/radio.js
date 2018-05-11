/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
        [
            'jquery',
            'ko',
            'Magestore_Webpos/js/view/base/form/element/abstract'
        ],
        function ($, ko, elementAbstract) {
            "use strict";
            return elementAbstract.extend({
                id: ko.observable(''),
                data: ko.observableArray([]),
                
                initialize: function () {
                    this._super();
                },
                
                render: function(){
                    var id = this.id.call() ? 'id="'+this.id.call()+'"' : '';
                    var name = this.data().name ? ' name="'+this.data().name+'"' : '';
                    var attr = id + name;
                    var optionHtml = '';
                    var elmentValue = this.data().value;
                    if(this.data().options)
                        $.each(this.data().options, function(index, value) {
                            var checked = elmentValue && elmentValue == index ? ' checked="checked"' : '';
                            optionHtml += '<input type="radio" '+name+' value="'+index+'"'+checked+'/>'+value;
                        });
                    return optionHtml;
                }
            });
        }
);
