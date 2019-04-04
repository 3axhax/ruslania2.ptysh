//(function() {
//    print = function() {
//        return new _Print();
//    };

    function _Print() {}
    _Print.prototype = {
        init: function(options) {
            this.setConst(options);
            this.setEvents();
            return this;
        },

        setConst: function(options) {
            this.$button = options.$button;
            this.$content = options.$content;
        },
        setEvents: function() {
            var self = this;
            this.$button.on('click', function(){
                self.printPopup();
                return false;
            });
        },

        printPopup: function() {
            var printWindow = window.open('', 'PRINT', 'height=600,width=800');
            if (printWindow) {
                printWindow.document.write();
                printWindow.document.write('<html><head><title>PRINT</title></head><body>');
                printWindow.document.write('<style>'+
                    '@media print {'+
                    '.bordered {'+
                    'margin-top: 10px;'+
                    'padding: 5px 5px 5px 5px;'+
                    '}'+
                    '.printed_btn {display: none;}'+
                    '.info_order div.row { margin: 4px 0 4px 0; }'+

                    '.info_order div { font-size: 13px; }'+
                    '.info_order div span.span1 { display: inline-block; width: 200px; }'+
                    '.info_order div div.span11 {'+
                    'width: 400px;'+
                    'display: inline-block;'+
                    'margin: 0;'+
                    'font-weight: bold;'+
                    '}'+
                    '.mbt10 {margin-bottom: 10px;}'+
                    'table.items_orders { margin: 25px 0; border: 1px solid #eee; }'+
                    'table.items_orders th { text-align: left; }'+
                    'table.items_orders tr.footer td div.summa div.itogo { height: 31px; float: right; line-height: 31px; }'+
                    'a {text-decoration: none; color: #000; }'+
                    '}'+
                    '</style>'+
                '');
                printWindow.document.write(this.$content.html());
                printWindow.document.write('</body></html>');
                printWindow.document.close(); // necessary for IE >= 10
                printWindow.focus(); // necessary for IE >= 10
                printWindow.print();
                printWindow.close();
                return true;
            }
            return false;
        }
    };

//}());