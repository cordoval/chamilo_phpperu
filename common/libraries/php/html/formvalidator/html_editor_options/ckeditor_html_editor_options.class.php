<?php
namespace common\libraries;
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
            $application = Request :: get('application');
            $app_sys_path = Path :: get(SYS_APP_PATH).$application.'/'.Path::RESOURCES_PATH.'/'.Path::RESOURCES_JAVASCRIPT_PATH.'/html_editor/ckeditor_configuration.js';
            if(\file_exists($app_sys_path))
            {
                $path = Path :: get(WEB_APP_PATH).$application.'/'.Path::RESOURCES_PATH.'/'.Path::RESOURCES_JAVASCRIPT_PATH.'/html_editor/ckeditor_configuration.js';
            }
            else
            {
                 $path = Path :: get(REL_PATH) . 'common/libraries/php/configuration/html_editor/ckeditor_configuration.js';
            }
	   $this->set_option(self :: OPTION_CONFIGURATION, $path);
	    $this->set_option(self :: OPTION_TEMPLATES, array(Path :: get(REL_PATH) . 'common/libraries/php/html/formvalidator/form_validator_html_editor_templates_instance.php'));
	}
}
?>