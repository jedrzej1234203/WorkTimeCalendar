//export to csv
(function($) {
    $.fn.tableExport = function(options) {
        var options = $.extend(
            {
                filename: 'absence table',
                format: 'csv',
                cols: '',
                head_delimiter: ',',
                column_delimiter: ',',
            },
            options
        );

        var $this = $(this);
        var cols = options.cols ? options.cols.split(',') : [];
        var result = '';
        var data_type = {
            csv: 'text/csv',
        };

        function getHeaders() {
            var th = $this.find('thead th');
            var arr = [];
            th.each(function(i, e) {
                cols.forEach(function(c) {
                    if (c === i + 1) {
                        arr.push(e.innerText);
                    }
                });
            });
            return arr;
        }

        function getItems() {
            var tr = $this.find('tbody tr:visible');
            var arr = [];

            tr.each(function(i, e) {
                var s = [];
                cols.forEach(function (c) {
                    s.push(
                        $(e)
                            .find('td:nth-child(' + c + ')')
                            .text()
                    );

                });
                arr.push(s);

            });

            return arr;
        }
        function download(data, filename, format) {
            var a = document.createElement('a');
            var blob = new Blob(['\ufeff', data], { type: data_type[format] });
            a.href = URL.createObjectURL(blob);
            var now = new Date();
            var time_arr = [
                'DD:' + now.getDate(),
                'MM:' + (now.getMonth() + 1),
                'YY:' + now.getFullYear(),
                'hh:' + now.getHours(),
                'mm:' + now.getMinutes(),
                'ss:' + now.getSeconds(),
            ];

            time_arr.forEach(function(item) {
                var key = item.split(':')[0];
                var val = item.split(':')[1];
                var regexp = new RegExp('%' + key + '%', 'g');
                filename = filename.replace(regexp, val);
            });

            a.download = filename + '.' + format;
            a.click();
        }
        var headers = getHeaders();
        var items = getItems();
        result += headers.join(options.head_delimiter) + '\n';
        items.forEach(function(item) {
            result += item.join(options.column_delimiter) + '\n';
        });
        download(result, options.filename, options.format);

    };
})(jQuery);
var absenceType;
//html table content and ajax calls to modify database
jQuery.ajax({
    url: ajaxurl,
    type: 'POST',
    data : {
        "action" : 'worktmp_show_table', "user_id": userId,},
    success(response) {
        jQuery("#worktmploaddata").html(response);
        jQuery("#worktmploaddata").on("click", ".worktmp_deletebutton", function() {
            elementId = this.value;
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data : {
                    "action" : 'worktmp_delete_absence', "data_id": elementId},
                success(){
                    jQuery("#"+elementId).remove();
                }
            });
        });
        jQuery("#worktmploaddata").on("click", ".worktmp_editbutton", function() {
            jQuery("#worktmploaddata").hide();
            jQuery("#editabsence").show();
            elementId = this.value;
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data : {
                    "action" : 'worktmp_get_field_data', "data_id": elementId, "field_name": 'absence_type' },
                success: function(response){
                    if (response === 'Absence') {
                        absenceType = 'option-1';
                    }
                    else  absenceType = 'option-2';
                    jQuery("#" + absenceType).prop("checked", true);
                }
            });
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data : {
                    "action" : 'worktmp_get_field_data', "data_id": elementId, "field_name": 'absence_start' },
                success: function(response){
                    jQuery("#absence_start").val(response);
                }
            });
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data : {
                    "action" : 'worktmp_get_field_data', "data_id": elementId, "field_name": 'absence_end' },
                success: function(response){
                    jQuery("#absence_end").val(response);
                }
            });
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data : {
                    "action" : 'worktmp_get_field_data', "data_id": elementId, "field_name": 'absence_comment' },
                success: function(response){
                    tinymce.activeEditor.setContent(response);
                }
            });
        })
        jQuery("#editabsence").on("click", "#worktmp_saveedit",function (){
            var absenceType = jQuery('[name=select_type]:checked').val();
            var absenceStart = jQuery("#absence_start").val();
            var absenceEnd = jQuery("#absence_end").val();
            var absenceComment = tinymce.activeEditor.getContent();
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data : {
                    "action" : 'worktmp_edit_absence', "data_id": elementId, "absence_type": absenceType,"absence_start": absenceStart, "absence_end": absenceEnd, "absence_comment": absenceComment },
            });
            jQuery("#editabsence").hide();
            location.reload();

        })
        jQuery("#editabsence").on("click", "#worktmp_discardedit", function(){
            jQuery("#worktmploaddata").show();
            jQuery("#editabsence").hide();


        })
    }

});
//search engines
jQuery("#searchname").on("keyup", function() {
    var value = jQuery(this).val();
    jQuery("table tr").each(function(index) {
        if (index !== 0) {
            $row = jQuery(this);
            var id = $row.find("td:first").text().toLowerCase();
            if (id.includes(value.toLowerCase())) {
                $row.show();
            }
            else {
                $row.hide();
            }
        }
    });
});
jQuery("#searchdate").on("keyup", function() {
    var value = jQuery(this).val();
    jQuery("table tr").each(function(index) {
        if (index !== 0) {
            $row = jQuery(this);
            var id = $row.find("td:eq( 2 )").text();
            if (id.includes(value)) {
                $row.show();
            }
            else {
                $row.hide();
            }
        }
    });
});
//download button
jQuery("#worktmp_downloadbutton").click(function() {
    jQuery('#worktmp_tabled').tableExport({
        format: 'csv',
        cols: '1,2,3,4',
    });
});
