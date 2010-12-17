$(function() {

    $('.ns_prefix').each(function(index){

        
        $(this).change(function(){

            $('#property_type_id').remove();
            $('#metadata_property_value').remove();

            var name = 'property_type_id';
            
            var target_element =  $(this).parent();

            var types = doAjaxPost("application/metadata/php/ajax/get_property_types.php", {id : $(this).val()});

            types = eval('(' + types + ')');

            var options = '';

            $.each(types, function(index, value){

                options += '<option value="' + index + '">' + value + '</option>';

            });

            target_element.append('<select id="' + name + '"  name="' + name + '">' + options + '</select><input type="text" id="metadata_property_value" name="value" />');
            setDefaults();
            
        });
    });

     function setDefaults()
    {
        $('select[name=property_type_id]').change(function(){
        target_element =  $('#metadata_property_value').parent();
        $('#metadata_property_value').remove();

        var defaults = doAjaxPost("application/metadata/php/ajax/get_metadata_defaults.php",{ property_type_id : $('select[name=property_type_id] option:selected').val()});
        defaults = eval('(' + defaults + ')');

        if(defaults.length > 1){

            var select_string = select_input();

            $.each(defaults, function(index, value){
                select_string += '<option value="'+value+'">'+value+'</option>';
            });

            select_string += '</select>';
            target_element.append(select_string);

        }else {

                target_element.append(text_input());
                if(defaults.length == 1){
                    $('#metadata_property_value').val(defaults[0]);
                }
            }
       });

       var text_input = function (){
           return '<input id="metadata_property_value" type="text" name="value"/>';
       };

       var select_input = function(){
            return '<select name="value" id="metadata_property_value">';
       };
    }
});


