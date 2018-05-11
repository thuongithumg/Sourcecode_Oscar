/**
 * Created by duongdiep on 08/04/2017.
 */
/**
 * Created by duongdiep on 19/02/2017.
 */


var Correctqty = Class.create();
Correctqty.prototype = {
    initialize: function(config) {
        this.gridJsObject = window[config.gridJsObjectName];
        this.correctQtyButton = config.correctQtyButton;
        this.urls = config.urls;
        //if(config.correctQtyButton)
        //    Event.observe(config.correctQtyButton, 'click', this.correctQty.bind(this));
        this.massCorrectProducts = config.massCorrectProducts;
        this.massCorrectWarehouse = config.massCorrectWarehouse;
        this.massTotalSize = config.massTotalSize;
        this.img = config.img;
        this.alt = config.alt;

        if(config.massCorrectProducts)
             this.correctQty();
    },

    /**
     * Change reload grid after change warehouse
     * @param event
     */
    correctQty : function(event){
        var self = this;
        new Ajax.Updater(
            {success: 'formLowestSuccess'}, this.urls, {
                method: 'post',
                parameters: {
                    product : this.massCorrectProducts,
                    warehouse : this.massCorrectWarehouse,
                },
                asynchronous: true,
                evalScripts: false,
                onComplete: function (request, json) {
                },
                onLoading: function (request, json) {
                    Element.show('loading-mask');
                },
                onSuccess: function (transport) {
                    if (transport.responseText) {
                        var json = JSON.parse(transport.responseText);
                        if(json.remain_size && json.remain_size >0){
                            self.changesloadingmask(json.remain_size);
                            self.continueCorrect(json);
                        }else{
                            self.changesloadingmask(0);
                            self.gridJsObject.doFilter();
                        }
                    }
                }
            }
        );

    },
    continueCorrect : function(json){
        var self = this;
        new Ajax.Updater(
            {success: 'formLowestSuccess'}, this.urls, {
                method: 'post',
                parameters: {
                    product : json.product_remain,
                    warehouse : json.warehouse_id,
                },
                asynchronous: true,
                evalScripts: false,
                onComplete: function (request, json) {
                },
                onLoading: function (request, json) {
                },
                onSuccess: function (transport) {
                    if (transport.responseText) {
                        var json = JSON.parse(transport.responseText);
                        if(json.remain_size && json.remain_size >0){
                            self.changesloadingmask(json.remain_size);
                            self.continueCorrect(json);
                        }else{
                            self.changesloadingmask(0);
                            self.gridJsObject.doFilter();
                        }

                    }
                }
            }
        );
    },
    changesloadingmask : function (size){
        var percent =  Math.ceil(((this.massTotalSize - size)/this.massTotalSize)*100);
        Element.hide('loading-mask');
        $('loading-mask').innerHTML = '<p class="loader" id="loading_mask_loader"><img src='+this.img+' alt='+this.alt+' /><br/> Updating ... ' +percent+ '%</p>';
        Element.show('loading-mask');
    },
}
