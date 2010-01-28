<?php

class FormValidatorCkeditorHtmlEditor extends FormValidatorHtmlEditor
{

    function create()
    {
        $form = $this->get_form();

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

        $attributes = $this->get_attributes();

        $editor = array();
        $editor[] = '<script type="text/javascript">';
        $editor[] = '$(function ()';
        $editor[] = '{';
        $editor[] = '	$(document).ready(function ()';
        $editor[] = '	{';
        $editor[] = '		$("textarea.html_editor[name=\''. $this->get_name() .'\']").ckeditor({';
        
        if (isset($attributes['toolbar']))
        {
        	$editor[] = '			toolbar : \'' . $attributes['toolbar'] . '\'';
        }
        
        $editor[] = '		});';
        $editor[] = '	});';
        $editor[] = '});';
        $editor[] = '</script>';

        $form->addElement('html', implode("\n", $editor));

        return parent :: create();
    }
}

?>