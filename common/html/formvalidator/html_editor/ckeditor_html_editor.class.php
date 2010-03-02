<?php
/**
 * Specific setting / additions for the CKEditor HTML editor
 * All CKEditor settings: http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.config.html
 *
 * @author Scaramanga
 */

class FormValidatorCkeditorHtmlEditor extends FormValidatorHtmlEditor
{
    function create()
    {
        $form = $this->get_form();

        $scripts = array();
        $scripts[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'html_editor/ckeditor/ckeditor_source.js');
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

        $javascript = array();
        $javascript[] = '<script type="text/javascript">';
        $javascript[] = '$(function ()';
        $javascript[] = '{';
        $javascript[] = '	$(document).ready(function ()';
        $javascript[] = '	{';
        $javascript[] = '		$("textarea.html_editor[name=\''. $this->get_name() .'\']").ckeditor({';
        $javascript[] = $this->get_options()->render_options();
        $javascript[] = '		});';
        $javascript[] = '	});';
        $javascript[] = '});';
        $javascript[] = '</script>';

        $form->addElement('html', implode("\n", $javascript));

        return parent :: create();
    }
}

?>