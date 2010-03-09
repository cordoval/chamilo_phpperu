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

        $scripts = $this->get_includes();

        foreach($scripts as $script)
        {
            if (!empty($script))
            {
                $form->addElement('html', $script);
            }
        }

        $form->addElement('html', implode("\n", $this->get_javascript()));

        return parent :: create();
    }

    function render()
    {
        $html = array();
        $html[] = parent :: render();
//        $html[] = implode("\n", $this->get_includes());
        $html[] = implode("\n", $this->get_javascript());

        return implode("\n", $html);
    }

    function get_includes()
    {
        $scripts = array();
        $scripts[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'html_editor/ckeditor/ckeditor.js');
        $scripts[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'html_editor/ckeditor/adapters/jquery.js');

        return $scripts;
    }

    function get_javascript()
    {
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

        return $javascript;
    }
}

?>