<?php
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Attributes/Alt.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Attributes/AttributeClass.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Attributes/AttributeMaxlength.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Attributes/Checked.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Attributes/Disabled.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Attributes/Readonly.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Attributes/Size.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Attributes/Source.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Attributes/Tabindex.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Attributes/Title.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Attributes/Accesskey.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Attributes/Rows.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Attributes/Cols.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Attributes/Style.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Attributes/Onclick.class.php';

abstract class Attribute
{
	protected $attribute;
	
	public function Attribute($attribute = null)
	{
		$this->attribute = $attribute;
	}	
}
?>