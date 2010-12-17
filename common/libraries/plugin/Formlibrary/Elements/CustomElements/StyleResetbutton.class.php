<?php

require_once ('plugin/FormLibrary/Elements/CustomElements/StyleButton.class.php');

class StyleResetbutton extends StyleButton
{
    public function StyleResetbutton($elementName = null, $elementLabel = null, $value = null)
    {
        parent :: StyleButton('reset', $elementName, $elementLabel, $value);
    }
}
?>