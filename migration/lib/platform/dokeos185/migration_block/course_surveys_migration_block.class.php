<?php

require_once dirname(__FILE__) . "/../data_class/dokeos185_survey_invitation.class.php";

require_once dirname(__FILE__) . "/../data_class/dokeos185_survey_answer.class.php";

require_once dirname(__FILE__) . "/../data_class/dokeos185_survey_question.class.php";

require_once dirname(__FILE__) . "/../data_class/dokeos185_survey_question_option.class.php";

require_once dirname(__FILE__) . "/../course_data_migration_block.class.php";

require_once dirname(__FILE__) . "/../data_class/dokeos185_survey.class.php";

class CourseSurveysMigrationBlock extends CourseDataMigrationBlock
{
	const MIGRATION_BLOCK_NAME = 'course_surveys';
	
	function get_prerequisites()
	{
		return array(CoursesMigrationBlock :: MIGRATION_BLOCK_NAME);
	}
	
	function get_block_name()
	{
		return self :: MIGRATION_BLOCK_NAME;
	}
	
	function get_course_data_classes()
	{
		return array(new Dokeos185Survey(), new Dokeos185SurveyQuestion(), new Dokeos185SurveyQuestionOption(), new Dokeos185SurveyAnswer(), new Dokeos185SurveyInvitation());
	}
}

?>