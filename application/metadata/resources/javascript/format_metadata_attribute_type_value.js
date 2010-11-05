$(function() {
    //alert('ok');
    $('.attribute_value').each(function(index){
        //alert('this');
        $(this).change(function(){
            if($(this).val() == 'value'){
                $(this).append();
            }else{
                var attributes = doAjaxPost("application/metadata/php/ajax/get_property_attribute_types.php");
                attributes = eval('(' + attributes + ')');
            }
        });
    });
});


