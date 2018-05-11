var ScanBarcode = function (data) {
    this.barcodes = {};
    for (var item in data) {
        ScanBarcode.prototype[item] = data[item];
    }
};
ScanBarcode.prototype = {
    render: function () {
        var table = $('barcode-table');
        for (var i in this.barcodes) {
            var rowElement = this.renderRow(this.barcodes[i]);
            table.down('tbody').prepend(rowElement);
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
                data.qty = 1;
                self.addBarcode(data);
            }
        });
    },
    getBarcode: function () {
        return $('barcode').value;
    },
    addBarcode: function (data) {
        if (data.barcode_id) {
            if (this.barcodeExist(data.barcode_id)) {
                this.updateOldBarcode(data.barcode_id, 1);
            } else {
                var table = $('barcode-table');
                var rowElement = this.renderRow(data);
                table.down('tbody').prepend(rowElement);
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
        $(barcode_id).value = Number.parseFloat($(barcode_id).value) + Number.parseFloat(qty);
    },
    renderRow: function (data) {
        var wrapper = document.createElement('tr');
        wrapper.innerHTML = "<tr>" +
            "<td>" + data.barcode + "</td>" +
            "<td><input class='barcode-qty' id='" + data.barcode_id + "' type='text' value='" + data.qty + "'></td>" +
            "<td>" + data.product_sku + "</td>" +
            "<td>" + data.product_name + "</td>" +
            "<td>" + data.supplier_code + "</td>" +
            "</tr>";
        return wrapper;
    },
    submitBarcode: function () {
        var params = {
            barcodes: JSON.stringify(this.getEditedBarcodes())
        };
        var self = this;
        new Ajax.Request(this.submitBarcodeUrl, {
            method: 'post',
            parameters: params,
            onComplete: function (response) {
                if (self.handleUrl) {
                    window.location.href = self.handleUrl;
                } else {
                    window.location.reload();
                }
            }
        });
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
    getModal: function(){
        return jQuery('#scan-barcode');
    }
};