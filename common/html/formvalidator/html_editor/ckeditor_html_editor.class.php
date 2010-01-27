<?php

class FormValidatorCkeditorHtmlEditor extends FormValidatorHtmlEditor
{

    function __construct($form, $name, $label, $required = true, $attributes = array())
    {
        parent :: __construct($form, $name, $label, $required, $attributes);

        $scripts = array();
        $scripts[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/html_editor/html_editor_ckeditor.js');
        $scripts[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'html_editor/ckeditor/ckeditor.js');
        $scripts[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'html_editor/ckeditor/adapters/jquery.js');

        foreach($scripts as $script)
        {
            if (!empty($script))
            {
                $form->addElement('html', $script);
            }
        }
    }
}

?>