<?php
/**
 * Specific setting / additions for the CKEditor HTML editor
 * All CKEditor settings: http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.config.html
 *
 * @author Scaramanga
 */
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/CustomElements/Html_editor/html_editor_options/ckeditor_html_editor_options.class.php';

class FormLibraryCkeditorHtmlEditor extends FormLibraryHtmlEditor
{
	function FormLibraryCkeditorHtmlEditor($name, $label, $required = true, $options = array())
    {
        parent::__construct($name, $label, $required, $options);    	        
    }
	
	function render()
    {
        $opts = new FormLibraryCkeditorHtmlEditorOptions($this->options);                       
    	$scripts = array();
    	$element = new TextArea($this->form, $this->name, "", $this->label);
    	$aclass = new AttributeClass($this->name);
    	$element->get_attributestorage()->add_attribute($aclass);
        $scripts[] = $element->render();
        $scripts[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'html_editor/ckeditor/ckeditor.js');
        $scripts[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'html_editor/ckeditor/adapters/jquery.js');
		$scripts[] = '<script type="text/javascript">';
		$scripts[] = "CKEDITOR.replace('" . $this->name . "', {";
		$scripts[] = $opts->render_options();
		$scripts[] = '});';
		$scripts[] = 'function get_content(){';
        $scripts[] = '$content = tinyMCE.get("'. $this->name. '").getContent();}'; 
		$scripts[] = '</script>';
    	return implode('', $scripts);
    }    
}
?>