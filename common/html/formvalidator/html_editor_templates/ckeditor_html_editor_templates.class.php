<?php
/**
 * The combination of options available for the FormValidatorCkeditorHtmlEditor
 *
 * @author Scaramanga
 */

class FormValidatorCkeditorHtmlEditorTemplates extends FormValidatorHtmlEditorTemplates
{
    function render()
    {
        $templates = $this->get_templates();

        if ($templates->size() == 0)
        {
            return $this->render_default_templates();
        }

        $html = array();

        $html[] = "CKEDITOR.addTemplates( 'default',";
        $html[] = '{';
        $html[] = "// The name of sub folder which hold the shortcut preview images of the templates.";
        $html[] = "imagesPath : CKEDITOR.getUrl( CKEDITOR.plugins.getPath( 'templates' ) + 'templates/images/' ),";
        $html[] = '';
        $html[] = '// The templates definitions';
        $html[] = 'templates :';
        $html[] = '[';

        $templates = $this->get_templates();

        $editor_templates = array();
        while ($template = $templates->next_result())
        {
            $description = preg_replace('/((\\\\n)+)/',"$1\"+\n\"",preg_replace("/(\r\n|\n)/",'\\n',addslashes($template->get_description())));
            $design = preg_replace('/((\\\\n)+)/',"$1\"+\n\"",preg_replace("/(\r\n|\n)/",'\\n',addslashes($template->get_design())));

            $editor_template = array();
            $editor_template[] = '{';
            $editor_template[] = '	title: \'' . addslashes($template->get_title()) . '\',';
            $editor_template[] = '	image: \'\',';
            $editor_template[] = '	description: "' . $description . '",';
            $editor_template[] = '	html: "' . $design . '"';
            $editor_template[] = '}';

            $editor_templates[] = implode("\n", $editor_template);
        }

        $html[] = implode(',', $editor_templates);
        $html[] = ']';
        $html[] = '});';

        return implode("\n", $html);
    }

    function render_default_templates()
    {

        return '';
    }
}
?>