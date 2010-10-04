<?php
/**
 * The combination of options available for the FormValidatorCkeditorHtmlEditor
 *
 * @author Scaramanga
 */

class FormValidatorCkeditorHtmlEditorOptions extends FormValidatorHtmlEditorOptions
{
	function get_mapping()
	{
	    $mapping = parent :: get_mapping();

	    $mapping[self :: OPTION_THEME] = 'skin';
	    $mapping[self :: OPTION_COLLAPSE_TOOLBAR] = 'toolbarStartupExpanded';
	    $mapping[self :: OPTION_CONFIGURATION] = 'customConfig';
	    $mapping[self :: OPTION_FULL_PAGE] = 'fullPage';
	    $mapping[self :: OPTION_TEMPLATES] = 'templates_files';

	    return $mapping;
	}

	function process_collapse_toolbar($value)
	{
	    if ($value === true)
	    {
	        return false;
	    }
	    else
	    {
	        return true;
	    }
	}

	function set_defaults()
	{
	    parent :: set_defaults();

	    $path = Path :: get(REL_PATH) . 'common/configuration/html_editor/ckeditor_configuration.js';
	    $this->set_option(self :: OPTION_CONFIGURATION, $path);
	    $this->set_option(self :: OPTION_TEMPLATES, array(Path :: get(REL_PATH) . 'common/html/formvalidator/form_validator_html_editor_templates_instance.php'));
	}
}
?>