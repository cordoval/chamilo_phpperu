<?php
 
require_once ('plugin/FormLibrary/Elements/CustomElements/StyleButton.class.php');


class StyleSubmitbutton extends StyleButton
{
    function StyleSubmitbutton($elementName = null, $elementLabel = null, $value = null)
    {
       parent :: StyleButton('submit', $elementName, $elementLabel, $value);
    }    
}
?>