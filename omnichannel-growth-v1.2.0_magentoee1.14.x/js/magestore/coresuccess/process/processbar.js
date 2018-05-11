/**
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
 * @package     Magestore_Coresuccess
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

var CoresuccessProcessBar = Class.create();
CoresuccessProcessBar.prototype = {
    initialize: function (containerId) {
        this.containerId = containerId;
        this.container = $(containerId);
        this.size = this.container.getWidth();
        this.updateValue(0);
    },
    setValue: function (value) {
        this.value = value;
    },
    updateValue: function (value) {
        this.value = value;
        var remainSize = parseInt((100 - value) / 100 * this.size);
        var remainPercent = parseInt(100 - value);
        var elements = this.container.select('div.remaining-process');
        elements[0].setStyle({width: remainPercent + '%', height: '100%', position: 'absolute', top: '0px', right: '0px'});
        var percentItems = this.container.select('span.percent');
        percentItems[0].update(value + '%');
        if (value > 0) {
            this.container.show();
        } else {
            this.container.hide();
        }
    }
}
