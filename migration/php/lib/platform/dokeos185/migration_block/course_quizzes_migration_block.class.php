<?php

require_once dirname(__FILE__) . '/../data_class/dokeos185_quiz.class.php';
require_once dirname(__FILE__) . '/../data_class/dokeos185_quiz_question.class.php';
require_once dirname(__FILE__) . '/../data_class/dokeos185_quiz_rel_question.class.php';
require_once dirname(__FILE__) . '/../data_class/dokeos185_quiz_answer.class.php';
require_once dirname(__FILE__) . '/../course_data_migration_block.class.php';

class CourseQuizzesMigrationBlock extends CourseDataMigrationBlock
{
	const MIGRATION_BLOCK_NAME = 'course_quizzes';
	
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
		return array(new Dokeos185Quiz(), new Dokeos185QuizQuestion(), new Dokeos185QuizAnswer());
	}
}

?>