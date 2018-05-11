var ScanBarcode = function (data) {
    this.barcodes = {};
    for (var item in data) {
        this[item] = data[item];
    }
    Event.observe($(this.input), 'keypress', function (event) {
        // charCode is on chorme browser , keyCode is on firefox browser
        if (event.charCode == 13 || event.keyCode == 13) {
            event.target.select();
            this.loadBarcode();
        }
    }.bind(this));
};
ScanBarcode.prototype = {
    render: function () {
        for (var i in this.barcodes) {
            var rowElement = this.renderRow(this.barcodes[i]);
            $(this.table).down('tbody').prepend(rowElement);
        }
    },
    loadBarcode: function () {
        var params = {
            barcode: this.getBarcode()
        };
        var self = this;
        new Ajax.Request(this.loadBarcodeUrl, {
            method: 'post',
            parameters: params,
            onComplete: function (response) {
                var data = response.responseJSON;
                self.addBarcode(data);
            }
        });
    },
    getBarcode: function () {
        return $(this.input).value;
    },
    addBarcode: function (data) {
        data.qty = 1;
        if (data.barcode_id) {
            if (this.barcodeExist(data.barcode_id)) {
                this.updateOldBarcode(data.barcode_id, data.qty);
            } else {
                var rowElement = this.renderRow(data);
                $(this.table).down('tbody').prepend(rowElement);
                this.barcodes[data.barcode_id] = data;
            }
        }
    },
    barcodeExist: function (barcode_id) {
        if (typeof this.barcodes[barcode_id] == 'undefined') {
            return false;
        }
        return true;
    },
    updateOldBarcode: function (barcode_id, qty) {
        this.barcodes[barcode_id].qty = Number.parseFloat(this.barcodes[barcode_id].qty) + Number.parseFloat(qty);
        if ($$('#' + this.table + ' #' + barcode_id)[0])
            $$('#' + this.table + ' #' + barcode_id)[0].value = Number.parseFloat($(barcode_id).value) + Number.parseFloat(qty);
    },
    renderRow: function (data) {
        var wrapper = document.createElement('tr');
        wrapper.innerHTML = "<tr>" +
            "<td>" + data.barcode + "</td>" +
            "<td><input disabled class='barcode-qty' id='" + data.barcode_id + "' type='text' value='1'></td>" +
            "<td>" + data.product_sku + "</td>" +
            "<td>" + data.product_supplier_sku + "</td>" +
            "<td>" + data.product_name + "</td>" +
            "</tr>";
        return wrapper;
    },
    submitBarcode: function () {
        var params = {
            barcodes: JSON.stringify(this.getEditedBarcodes())
        };
        var result = this.submit(params);
        if (!(result === false)) {
            this.barcodes = {};
            $(this.table).down('tbody').innerHTML = '';
            $(this.input).value = '';
        }
    },
    getEditedBarcodes: function () {
        for (var i in this.barcodes) {
            if (this.barcodes[i]) {
                var qty = $(this.barcodes[i].barcode_id).value;
                this.barcodes[i].qty = qty;
            }
        }
        return this.barcodes;
    },

    submit: function (params) {

    }
};