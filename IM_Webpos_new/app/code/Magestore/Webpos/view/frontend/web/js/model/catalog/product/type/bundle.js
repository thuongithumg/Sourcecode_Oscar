/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
        [
            'jquery',
            'underscore',
            'Magestore_Webpos/js/model/catalog/product/type/composite',
        ],
        function ($, _, typeComposite) {
            "use strict";
            return typeComposite.extend({
                childStocks: {},
                getChildProductIds: function () {
                    var childIds = [];
                    var childs = this.getProduct().data.bundle_options;
                    for (var i in childs) {
                        var item = childs[i];
                        for (var j in item.items) {
                            var cItem = item.items[j];
                            childIds.push(parseInt(cItem.entity_id));
                        }
                    }
                    childIds = _.uniq(childIds);
                    return childIds;
                },
                isSalable: function (childId) {
                    /* check isSalable of child */
                    if (childId) {
                        return this._super(childId);
                    }
                    //if (!this.salableChecked) {
                        /* check isalable of parent stock */
                        this.getProduct().data.stock.qty=9999;
                        if(this.getChildStock(this.getProduct().data.id)) {
                            if(!this.isSalable(this.getProduct().data.id)) {
                               this.is_salable = false;
                               this.salableChecked = true;
                               return this.is_salable;
                            }
                        }                        
                        /* check isslable of all childs */
                        this.is_salable = this.isSalableAllItem();
                        this.salableChecked = true;
                   // }
                    return this.is_salable;
                },
                isSalableAllItem: function () {
                    this.is_salable = false;
                    var selections = this.getProduct().data.bundle_options;
                    var requiredOptionIds = {};
                    var salableSelectionCount = 0;
                    for (var i in selections) {
                        /* not required selection */
                        if (selections[i].required != 1) {
                            continue;
                        }
                        requiredOptionIds[selections[i].id] = 0;
                        for (var j in selections[i].items) {
                            /* get child item & child-stock */
                            var child = selections[i].items[j];
                            var childStock = this.getChildStock(child.entity_id);
                            var qty = this.getQty(child.entity_id);
                            var isManageStock = this.isManageStock(child.entity_id);
                            var isInStock = this.isInStock(child.entity_id);
                            var isBackorder = this.isBackorder(child.entity_id);
                            var selectionEnoughQty = false;
                            /* do not manage stock */
                            if (!isManageStock) {
                                selectionEnoughQty = true;
                            } else if(!isInStock) {
                                selectionEnoughQty = false;
                            } else if(isBackorder > 0){
                                selectionEnoughQty = true;
                            } else {
                                selectionEnoughQty = (qty >= parseFloat(child.selection_qty));
                            }
                            
                            /* this child is salable */
                            if (/*child.selection_can_change_qty == '1' ||*/ selectionEnoughQty) {
                                if (requiredOptionIds[selections[i].id] != 1) {
                                    salableSelectionCount++;
                                    requiredOptionIds[selections[i].id] = 1;
                                }
                            }
                        }
                    }
                    /* all required childs are salable */
                    if (salableSelectionCount == _.keys(requiredOptionIds).length) {
                        this.is_salable = true;
                    }
                    return this.is_salable;
                }
            });
        }
);