$(function() {

    $('.attribute_value').each(function(index){

        $(this).change(function(){

            var name = 'metadata_property_attribute_value_value_' + $(this).attr('id');
            
            $('#attribute_value').remove();

            var target_element =  $(this).parent();
            //alert(name);
            if($(this).val() == 2){

                target_element.append('<input type="text" name="' + name + '"  id="attribute_value" />');

            }else{

                var attributes = doAjaxPost("application/metadata/php/ajax/get_property_attribute_types.php");

                attributes = eval('(' + attributes + ')');

                var options = '';

                $.each(attributes, function(index, value){

                    options += '<option value="' + index + '">' + value + '</option>';

                });

                target_element.append('<select id="attribute_value"  name="' + name + '">' + options + '</select>');
            }
        });
    });
});


