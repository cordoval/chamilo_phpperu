<?php

class FormValidatorTinymceHtmlEditor extends FormValidatorHtmlEditor
{
    function create()
    {
        $scripts = array();
        $scripts[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/html_editor/html_editor_tinymce.js');
        $scripts[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'html_editor/tinymce/tiny_mce.js');
        $scripts[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'html_editor/tinymce/jquery.tinymce.js');

        foreach($scripts as $script)
        {
            if (!empty($script))
            {
                $this->get_form()->addElement('html', $script);
            }
        }

        return parent :: create();
    }
}

?>