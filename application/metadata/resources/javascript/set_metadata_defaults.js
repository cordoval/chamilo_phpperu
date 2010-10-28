/*global $, document, jQuery, window */

$(function () {
    $('select[name=property_type_id]').change(function(){
        target_element =  $('#metadata_property_value').parent();
        $('#metadata_property_value').remove();
        
        var defaults = doAjaxPost("application/metadata/php/ajax/get_metadata_defaults.php",{ property_type_id : $('select[name=property_type_id] option:selected').val()});
        defaults = eval('(' + defaults + ')');

        if(defaults.length > 1){

            var select_string = '<select name="value" id="metadata_property_value">';

            $.each(defaults, function(index, value){
                select_string += '<option value="'+value+'">'+value+'</option>';
            });

            select_string += '</select>';
            target_element.append(select_string);

    }else {

            target_element.append('<input id="metadata_property_value" type="text" name="value" />');
            if(defaults.length == 1){
                $('#metadata_property_value').val(defaults[0]);
            }
        }
   });
});




