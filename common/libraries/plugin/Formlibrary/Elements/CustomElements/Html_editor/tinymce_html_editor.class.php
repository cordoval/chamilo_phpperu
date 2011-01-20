<?php
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/CustomElements/Html_editor/html_editor_options/tinymce_html_editor_options.class.php';

class FormLibraryTinymceHtmlEditor extends FormLibraryHtmlEditor
{
	function FormLibraryTinyHtmlEditor($name, $label, $required = true, $options = array())
    {
        parent::__Construct($name, $label, $required, $options);    	        
    }

    function render()
    {
        $opts = new FormLibraryTinymceHtmlEditorOptions($this->options);                       
    	$scripts = array();
        $scripts[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'html_editor/tinymce/tiny_mce.js');
        $scripts[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'html_editor/tinymce/jquery.tinymce.js');        
        $scripts[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/html_editor/html_editor_tinymce.js');
		$scripts[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/html_editor/html_editor_tinymce.js');
    	$scripts[] = '<script type="text/javascript">';
		$scripts[] = 'tinyMCE.init({';            
        $scripts[] = $opts->render_options();;
        $scripts[] = ',editor_selector:"' . $this->get_name() .'",';  
        $scripts[] = 'mode:"specific_textareas"';        
        $scripts[] = '});';
        $scripts[] = 'function get_content(){';
        $scripts[] = '$content = tinyMCE.get("'. $this->name. '").getContent();}'; 
        $scripts[] = '</script>';
    	
    	$element = new TextArea($this->form, $this->name, "", $this->label);
    	$aclass = new AttributeClass($this->name);
    	$element->get_attributestorage()->add_attribute($aclass);
        $scripts[] = $element->render();
        
    	return implode($scripts);
    }
}
?>