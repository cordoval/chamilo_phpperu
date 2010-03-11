<?php
/**
 * The combination of options available for the FormValidatorCkeditorHtmlEditor
 *
 * @author Scaramanga
 */

class FormValidatorTinymceHtmlEditorTemplates extends FormValidatorHtmlEditorTemplates
{
    const TEMPLATE_ID = 'template';

    function render()
    {
        $template_id = Request :: get(self :: TEMPLATE_ID);

        if (isset($template_id))
        {
            return $this->render_template($template_id);
        }
        else
        {
            return $this->render_template_list();
        }
    }

    function render_template($template_id)
    {
    	$object = RepositoryDataManager :: get_instance()->retrieve_content_object($template_id);
    	return $object->get_design();
    }

    function render_template_list()
    {
        $templates = $this->get_templates();

        if ($templates->size() == 0)
        {
            return $this->render_default_templates();
        }

        $html = array();

        $html[] = "var tinyMCETemplateList = [";

        $templates = $this->get_templates();

        $editor_templates = array();
        while ($template = $templates->next_result())
        {
            $description = preg_replace('/((\\\\n)+)/',"$1\"+\n\"",preg_replace("/(\r\n|\n)/",'\\n',addslashes($template->get_description())));
            $design = preg_replace('/((\\\\n)+)/',"$1\"+\n\"",preg_replace("/(\r\n|\n)/",'\\n',addslashes($template->get_design())));

            $editor_template = array();
            $editor_template[] = '[';
            $editor_template[] = '"' . addslashes($template->get_title()) . '", ';
            $editor_template[] = '"' . Path :: get(REL_PATH) . 'common/html/formvalidator/form_validator_html_editor_templates_instance.php?' . self :: TEMPLATE_ID . '=' . $template->get_id() . '",';
            $editor_template[] = '"' . $description . '"';
            $editor_template[] = ']';

            $editor_templates[] = implode('', $editor_template);
        }

        $html[] = implode(',', $editor_templates);
        $html[] = '];';

        return implode("\n", $html);
    }

    function render_default_templates()
    {
        return '';
    }
}
?>