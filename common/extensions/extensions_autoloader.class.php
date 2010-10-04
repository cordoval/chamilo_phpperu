<?php
/**
 * $Id: extensions_autoloader.class.php 236 2009-11-16 12:56:59Z scaramanga $
 * @author vanpouckesven
 * @package application.common
 */

class ExtensionsAutoloader
{
	static function load($classname)
	{
		if(self :: check_for_calendar_files($classname))
		{
			return true;
		}

		if(self :: check_for_special_files($classname))
		{
			return true;
		}

		return false;
	}

	static function check_for_calendar_files($classname)
	{
		$lower_case = Utilities :: camelcase_to_underscores($classname);

		if(strpos($lower_case, '_calendar') !== false && strpos($lower_case, 'external_calendar') === false)
		{
			require_once dirname(__FILE__) . '/calendar/' . $lower_case . '.class.php';
		}
	}

	static function check_for_special_files($classname)
	{
		$list = array('category_manager' => 'category_manager/category_manager.class.php',
					  'feedback_manager' => 'feedback_manager/feedback_manager.class.php',
					  'invitation_manager' => 'invitation_manager/invitation_manager.class.php',
					  'invitation' => 'invitation_manager/invitation.class.php',
					  'invitation_form' => 'invitation_manager/invitation_form.class.php',
					  'invitation_support' => 'invitation_manager/invitation_support.class.php',
					  'invitation_parameters' => 'invitation_manager/invitation_parameters.class.php',
					  'repo_viewer' => 'repo_viewer/repo_viewer.class.php',
					  'repo_viewer_interface' => 'repo_viewer/repo_viewer_interface.class.php',
					  'reporting_viewer' => 'reporting_viewer/reporting_viewer.class.php',
				      'external_repository_manager' => 'external_repository_manager/external_repository_manager.class.php',
				      'external_repository_object' => 'external_repository_manager/external_repository_object.class.php',
				      'external_repository_object_display' => 'external_repository_manager/external_repository_object_display.class.php',
		              'external_repository_component' => 'external_repository_manager/external_repository_component.class.php',
		              'external_repository_connector' => 'external_repository_manager/external_repository_connector.class.php',
					  'external_repository_menu' => 'external_repository_manager/external_repository_menu.class.php',
					  'external_repository_object_renderer' => 'external_repository_manager/external_repository_object_renderer.class.php',
				      'dynamic_form_manager' => 'dynamic_form_manager/dynamic_form_manager.class.php',
				      'rights_editor_manager' => 'rights_editor_manager/rights_editor_manager.class.php',
					  'validation_manager' => 'validation_manager/validation_manager.class.php',
//					  'web_application' => 'web_application.class.php',
//					  'web_application_component' => 'web_application_component.class.php',
					  'test_category_manager' => 'category_manager/test_category_manager.class.php',
					  'email_manager' => 'email_manager/email_manager.class.php',
					  'video_conferencing_manager' => 'video_conferencing_manager/video_conferencing_manager.class.php',
				      'video_conferencing_meeting_object' => 'video_conferencing_manager/video_conferencing_meeting_object.class.php',
		              'video_conferencing_participants_object' => 'video_conferencing_manager/video_conferencing_participants_object.class.php',
				      'video_conferencing_connector' => 'video_conferencing_manager/video_conferencing_connector.class.php',
					  'video_conferencing_menu' => 'video_conferencing_manager/video_conferencing_menu.class.php',
					  'video_conferencing_meeting_object_renderer' => 'video_conferencing_manager/video_conferencing_meeting_object_renderer.class.php',
					  'video_conferencing_participants_object_renderer' => 'video_conferencing_manager/video_conferencing_participants_object_renderer.class.php',
					  'video_conferencing_meeting_object_display' => 'video_conferencing_manager/video_conferencing_meeting_object_renderer.class.php',
					  'video_conferencing_participants_object_display' => 'video_conferencing_manager/video_conferencing_participants_object_renderer.class.php');

		$lower_case = Utilities :: camelcase_to_underscores($classname);

		if(key_exists($lower_case, $list))
		{
			$url = $list[$lower_case];
			require_once dirname(__FILE__) . '/' . $url;
			return true;
		}

		return false;
	}
}

?>