/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'Magestore_Webpos/js/view/settings/general/element/select',
    ],
    function (Select) {
        "use strict";

        return Select.extend({
            defaults: {
                elementName: 'os_store_credit.show_customer_credit_balance_on_receipt',
                configPath: 'os_store_credit/show_customer_credit_balance_on_receipt'
            }
        });
    }
);