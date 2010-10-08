<?php
/**
 * @package common.html.formvalidator.Element.html_editor
 * $Id: fckeditor_html_editor.php 128 2009-11-09 13:13:20Z vanpouckesven $
 */
require_once Path :: get_library_path() . 'html/formvalidator/Element/html_editor.php';
require_once Path :: get_plugin_path() . 'html_editor/fckeditor/fckeditor.php';

class HTML_QuickForm_fckeditor_html_editor extends HTML_QuickForm_html_editor
{

    function HTML_QuickForm_fckeditor_html_editor($elementName = null, $elementLabel = null, $attributes = null, $options = array())
    {
        parent :: __construct($elementName, $elementLabel, $attributes, $options);
    }

    function set_type()
    {
        $this->_type = 'fckeditor_html_editor';
    }

    /**
     * Check if the browser supports FCKeditor
     *
     * @access public
     * @return boolean
     */
    function browserSupported()
    {
        return FCKeditor :: IsCompatible();
    }

    /**
     * Build this element using FCKeditor
     */
    function build_editor()
    {
        global $language_interface;
        if (! $this->browserSupported())
        {
            return $this->render_textarea();
        }
        
        $adm = AdminDataManager :: get_instance();
        $editor_lang = $adm->retrieve_language_from_english_name($language_interface)->get_isocode();
        $language_file = Path :: get_plugin_path() . 'html_editor/fckeditor/editor/lang/' . $editor_lang . '.js';
        if (empty($editor_lang) || ! file_exists($language_file))
        {
            //if there was no valid iso-code, use the english one
            $editor_lang = 'en';
        }
        
        $name = $this->getAttribute('name');
        $result[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'html_editor/fckeditor/fckeditor.js');
        
        $result[] = '<div style="display: inline;">';
        $result[] = '<script type="text/javascript">';
        $result[] = "\n/* <![CDATA[ */\n";
        $result[] = 'var oFCKeditor = new FCKeditor( \'' . $name . '\' ) ;';
        $result[] = 'oFCKeditor.BasePath = "' . Path :: get(WEB_PLUGIN_PATH) . 'html_editor/fckeditor/";';
        $result[] = 'oFCKeditor.Width = "' . $this->get_option('width') . '";';
        $result[] = 'oFCKeditor.Height = ' . ($this->get_option('full_page') ? '500' : $this->get_option('height')) . ';';
        $result[] = 'oFCKeditor.Config[ "FullPage" ] = ' . ($this->get_option('full_page') ? 'true' : 'false') . ';';
        $result[] = 'oFCKeditor.Config[ "DefaultLanguage" ] = "' . $editor_lang . '" ;';
        $result[] = 'oFCKeditor.Value = "' . str_replace('"', '\"', str_replace(array("\r\n", "\n", "\r", "/"), array(' ', ' ', ' ', '\/'), $this->getValue())) . '" ;';
        $result[] = 'oFCKeditor.ToolbarSet = \'' . $this->get_option('toolbar_set') . '\';';
        $result[] = 'oFCKeditor.Config[ "SkinPath" ] = oFCKeditor.BasePath + "editor/skins/' . Theme :: get_theme() . '/";';
        $result[] = 'oFCKeditor.Config["CustomConfigurationsPath"] = "' . Path :: get(WEB_LIB_PATH) . 'configuration/html_editor/fckconfig.js";';
        $result[] = 'oFCKeditor.Config[ "ToolbarStartExpanded" ] = ' . ($this->get_option('show_toolbar') ? 'true' : 'false') . ';';
        $result[] = 'oFCKeditor.Create();';
        $result[] = "\n/* ]]> */\n";
        $result[] = '</script>';
        $result[] = '<noscript>' . $this->render_textarea() . '</noscript>';
        if ($this->get_option('show_tags'))
        {
            $result[] = '<br/><small><a href="#" onclick="MyWindow=window.open(' . "'" . Path :: get(WEB_LIB_PATH) . "html/allowed_html_tags.php?fullpage=" . ($this->fullPage ? '1' : '0') . "','MyWindow','toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=500,height=600,left=200,top=20'" . '); return false;">' . Translation :: get('AllowedHTMLTags') . '</a></small><br />';
        }
        $result[] = '</div>';
        @mkdir(Path :: get(SYS_PATH) . 'files/fckeditor/' . Session :: get_user_id() . '/');
        return implode("\n", $result);
    }
}
?>