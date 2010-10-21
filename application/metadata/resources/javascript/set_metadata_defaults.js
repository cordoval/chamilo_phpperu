/*global $, document, jQuery, window */

$(function () {
    $('select[name=property_type_id]').change(function(){
        alert('ok'+ $('select[name=property_type_id] option:selected').val());
        //ajax get proper value
        var defaults = $.ajax({
            type: "POST",
            url: "application/lib/metadata/javascript/ajax/get_metadata_defaults.php",
            data: { property_type_id : $('select[name=property_type_id] option:selected').val()},
            async: false
        }).responseText;

        //handle data
    });
});


