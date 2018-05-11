/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
        [
            'jquery',
            'ko',
            'Magestore_Webpos/js/view/base/grid/abstract',
            'Magestore_Webpos/js/view/base/grid/renderer/price'
        ],
        function ($, ko, listAbstract, priceRender) {
            "use strict";

            return listAbstract.extend({
                initialize: function () {
                    this._super();
                },
                _prepareItems: function () {
                    var items = [
                        {name: "Well-Travelled Kitten", sales: 352, price: 75.95},
                        {name: "Speedy Coyote", sales: 89, price: 190.00},
                        {name: "Furious Lizard", sales: 152, price: 25.00},
                        {name: "Indifferent Monkey", sales: 1, price: 99.95},
                        {name: "Brooding Dragon", sales: 0, price: 6350},
                        {name: "Ingenious Tadpole", sales: 39450, price: 0.35},
                        {name: "Optimistic Snail", sales: 420, price: 1.50}
                    ];
                    this.addItems(items);
                },
                _prepareColumns: function () {
                    this.addColumn({headerText: "Item Name", rowText: "name", renderer: ''});
                    this.addColumn({headerText: "Sales Count", rowText: "sales", renderer: ''});
                    this.addColumn({headerText: "Price", rowText: "price", renderer: priceRender()});
                },
            });
        }
);
