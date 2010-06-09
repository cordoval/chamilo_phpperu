<?php
/**
 * @package repository.lib.content_object.survey.manage.context
 *
 * @author Eduard Vossen
 * @author Hans De Bisschop
 */
class SurveyContextManager extends SubManager
{
	function get_application_component_path()
	{
		return Path :: get_repository_path() . 'lib/content_object/survey/manage/context/component/';
	}

	function run()
	{
	    $this->display_header();
	    echo 'Implementation of survey context management goes here';
	    $this->display_footer();
	}
}

?>