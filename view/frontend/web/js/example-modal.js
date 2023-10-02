define([
    "jquery", "Magento_Ui/js/modal/modal",'mage/url'
], function($,modal,url){
    var options= {
        wrapperClass: 'devhub-modals-wrapper',
        modalClass: 'devhub-modal',
        overlayClass: 'devhub-modals-overlay',
        responsiveClass: 'devhub-modal-slide',
        type: 'popup',
        responsive: true,
        innerScroll: true,
        title: 'Stock Status',
        buttons: [
            {
                text: $.mage.__('Close Model'),
                class: 'devhub-popup-button',
                click: function (data) {
                    $('#devhub-stock-status-modal').modal('closeModal');
                }
            }
        ]
    };

    $(document).on('click', '.stock-status', function (e) {
        e.preventDefault();
        var dataOrderAttr = $(this).data('order');
        modal(options, $("#devhub-stock-status-modal"));
        $("#devhub-stock-status-modal").modal('openModal');
        $(".table-data").remove();
        if (dataOrderAttr){
            $.each(dataOrderAttr, function(index, value) {
                appendData(value);
            });
        }else {
            var name = $(this).attr('data-order-name');
            var qty = $(this).attr('data-order-qty');
            appendData(name, qty);
        }
        $(".action-close").remove();
    });

    function appendData(value = null, quantity = null) {
        var width;
        var newRow = $("<tr class='table-data'>");
        var namedata = $("<td>");
        var qtydata = $("<td>");
        var statusdata = $("<td>");
        if (quantity != null) {
            namedata.text(value);
            qtydata.text(quantity);
            width = (quantity / 100) * 100 + '%';
        } else {
            namedata.text(value.name);
            qtydata.text(value.qty);
            width = (value.qty / 100) * 100 + '%';
        }
        let progressbar = $('<div class="progress" role="progressbar" ' +
            'aria-label="Example with label" ' + 'aria-valuenow="25" aria-valuemin="0"' +
            ' aria-valuemax="100"></div>');
        let progress = $(`<div class="progress-bar" style="width:"></div>`)
        progress.css('width', width);
        progressbar.append(progress);
        statusdata.append(progressbar);
        newRow.append(namedata);
        newRow.append(qtydata);
        newRow.append(statusdata);
        $('.table-body').append(newRow);
    }
});

