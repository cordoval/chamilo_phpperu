<?php
/**
 * Specific setting / additions for the CKEditor HTML editor
 * All CKEditor settings: http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.config.html
 * 
 * @author Scaramanga
 */

class FormValidatorCkeditorHtmlEditor extends FormValidatorHtmlEditor
{
	private $options_map = array(	parent :: SETTING_TOOLBAR			=> 'toolbar',
									parent :: SETTING_LANGUAGE			=> 'defaultLanguage',
									parent :: SETTING_THEME				=> 'theme',
									parent :: SETTING_WIDTH				=> 'width',
									parent :: SETTING_HEIGHT			=> 'height',
									parent :: SETTING_COLLAPSE_TOOLBAR	=> 'toolbarStartupExpanded',
									parent :: SETTING_CONFIGURATION		=> 'customConfig',
									parent :: SETTING_FULL_PAGE			=> 'fullPage',
									parent :: SETTING_ENTER_MODE		=> 'enterMode',
									parent :: SETTING_SHIFT_ENTER_MODE	=> 'shiftEnterMode',
									parent :: SETTING_TEMPLATES			=> 'templates');

    function create()
    {
        $form = $this->get_form();
        $options = $this->get_options();
        $this->set_default_options();

        $scripts = array();
        $scripts[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'html_editor/ckeditor/ckeditor.js');
        $scripts[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'html_editor/ckeditor/adapters/jquery.js');

        foreach($scripts as $script)
        {
            if (!empty($script))
            {
                $form->addElement('html', $script);
            }
        }
        
//        $result[] = 'oFCKeditor.BasePath = "' . Path :: get(WEB_PLUGIN_PATH) . 'html_editor/fckeditor/";';
//        $result[] = 'oFCKeditor.Width = "' . $this->get_option('width') . '";';
//        $result[] = 'oFCKeditor.Height = ' . ($this->get_option('full_page') ? '500' : $this->get_option('height')) . ';';
//        $result[] = 'oFCKeditor.Config[ "FullPage" ] = ' . ($this->get_option('full_page') ? 'true' : 'false') . ';';
//        $result[] = 'oFCKeditor.Config[ "DefaultLanguage" ] = "' . $editor_lang . '" ;';
//        $result[] = 'oFCKeditor.Value = "' . str_replace('"', '\"', str_replace(array("\r\n", "\n", "\r", "/"), array(' ', ' ', ' ', '\/'), $this->getValue())) . '" ;';
//        $result[] = 'oFCKeditor.ToolbarSet = \'' . $this->get_option('toolbar_set') . '\';';
//        $result[] = 'oFCKeditor.Config[ "SkinPath" ] = oFCKeditor.BasePath + "editor/skins/' . Theme :: get_theme() . '/";';
//        $result[] = 'oFCKeditor.Config["CustomConfigurationsPath"] = "' . Path :: get(WEB_LIB_PATH) . 'configuration/html_editor/fckconfig.js";';
//        $result[] = 'oFCKeditor.Config[ "ToolbarStartExpanded" ] = ' . ($this->get_option('show_toolbar') ? 'true' : 'false') . ';';

        $attributes = $this->get_attributes();

        $javascript = array();
        $javascript[] = '<script type="text/javascript">';
        $javascript[] = '$(function ()';
        $javascript[] = '{';
        $javascript[] = '	$(document).ready(function ()';
        $javascript[] = '	{';
        $javascript[] = '		$("textarea.html_editor[name=\''. $this->get_name() .'\']").ckeditor({';
        $javascript[] = '			toolbar : \'Basic\'';
        $javascript[] = '		});';
        $javascript[] = '	});';
        $javascript[] = '});';
        $javascript[] = '</script>';

        $form->addElement('html', implode("\n", $javascript));

        return parent :: create();
    }
    
//        if (isset($options['toolbar']))
//        {
//        	$editor[] = '			toolbar : \'' . $options['toolbar'] . '\'';
//        }
//        else
//        {
//        	$editor[] = '			toolbar : \'Basic\'';
//        }
    function get_editor_options()
    {
    	$options = $this->get_options();
    	
    	dump($options);
    	$javascript = array();
    	
    	foreach($this->options_map as $key => $setting)
    	{
    		if (isset($options[$key]))
    		{
    			$javascript[] = '			' . $setting . ' : \'' . $options[$key] . '\'';
    		}
    	}
    	
    	return implode(",\n", $javascript);
    }
    
    function set_default_options()
    {
    	$options = $this->get_options();
    	$this->set_option('toolbar', 'Basic');
    }
}

?>