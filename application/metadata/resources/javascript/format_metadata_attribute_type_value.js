$(function() {

    $('.attribute_value').each(function(index){

        $(this).change(function(){

            var name = 'metadata_attribute_value_value_' + $(this).attr('id');
            var target_element =  $(this).parent();

            if($(this).val() == 2){

                target_element.append('<input type="text" name="' + name + '" />');

            }else{

                var attributes = doAjaxPost("application/metadata/php/ajax/get_property_attribute_types.php");

                attributes = eval('(' + attributes + ')');

                var options = '';

                $.each(attributes, function(index, value){

                    options += '<option value="' + index + '" name="' + name + '">' + value + '</option>';

                });

                target_element.append('<select name="value">' + options + '</select>');
            }
        });
    });
});


