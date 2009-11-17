<?php
/**
 * @package common.html.formvalidator.Element.html_editor
 * $Id: tinymce_html_editor.php 128 2009-11-09 13:13:20Z vanpouckesven $
 */
require_once Path :: get_library_path() . 'html/formvalidator/Element/html_editor.php';

class HTML_QuickForm_tinymce_html_editor extends HTML_QuickForm_html_editor
{

    function HTML_QuickForm_tinymce_html_editor($elementName = null, $elementLabel = null, $attributes = null, $options = array())
    {
        parent :: __construct($elementName, $elementLabel, $attributes, $options);
    }

    function set_type()
    {
        $this->_type = 'tinymce_html_editor';
    }

    /**
     * Check if the browser supports FCKeditor
     *
     * @access public
     * @return boolean
     */
    function browserSupported()
    {
        return true;
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
        $language_file = Path :: get_plugin_path() . 'fckeditor/editor/lang/' . $editor_lang . '.js';
        if (empty($editor_lang) || ! file_exists($language_file))
        {
            //if there was no valid iso-code, use the english one
            $editor_lang = 'en';
        }
        
        $name = $this->getAttribute('name');
        $result[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'html_editor/tinymce/tiny_mce.js');
        $result[] = '<script type="text/javascript">';
        $result[] = "\n/* <![CDATA[ */\n";
        $result[] = 'tinyMCE.init({';
        $result[] = '// General options';
        $result[] = 'mode : "textareas",';
        $result[] = 'theme : "advanced",';
        $result[] = 'plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,imagemanager,filemanager",';
        $result[] = '// Theme options';
        $result[] = 'theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",';
        $result[] = 'theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",';
        $result[] = 'theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",';
        $result[] = 'theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",';
        $result[] = 'theme_advanced_toolbar_location : "top",';
        $result[] = 'theme_advanced_toolbar_align : "left",';
        $result[] = 'theme_advanced_statusbar_location : "bottom",';
        $result[] = 'theme_advanced_resizing : true,';
        $result[] = '// Drop lists for link/image/media/template dialogs';
        $result[] = 'template_external_list_url : "js/template_list.js",';
        $result[] = 'external_link_list_url : "js/link_list.js",';
        $result[] = 'external_image_list_url : "js/image_list.js",';
        $result[] = 'media_external_list_url : "js/media_list.js",';
        $result[] = '});';
        $result[] = "\n/* ]]> */\n";
        $result[] = '</script>';
        $result[] = $this->render_textarea();
        $result[] = '<br/><small><a href="#" onclick="MyWindow=window.open(' . "'" . Path :: get(WEB_LIB_PATH) . "html/allowed_html_tags.php?fullpage=" . ($this->fullPage ? '1' : '0') . "','MyWindow','toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=500,height=600,left=200,top=20'" . '); return false;">' . Translation :: get('AllowedHTMLTags') . '</a></small>';
        //@mkdir(Path :: get(SYS_PATH).'files/fckeditor/'. Session :: get_user_id().'/');
        return implode("\n", $result);
    }
}
?>
