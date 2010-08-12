<?php
require_once dirname(__FILE__) . '/../data_class/dokeos185_lp.class.php';
require_once dirname(__FILE__) . '/../data_class/dokeos185_lp_item.class.php';
require_once dirname(__FILE__) . '/../data_class/dokeos185_lp_item_view.class.php';
require_once dirname(__FILE__) . '/../data_class/dokeos185_lp_view.class.php';
require_once dirname(__FILE__) . '/../data_class/dokeos185_lp_iv_objective.class.php';
require_once dirname(__FILE__) . '/../data_class/dokeos185_lp_iv_interaction.class.php';
require_once dirname(__FILE__) . '/../course_data_migration_block.class.php';

class CourseLearningPathsMigrationBlock extends CourseDataMigrationBlock
{
	const MIGRATION_BLOCK_NAME = 'course_learning_paths';
	
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
		return array(new Dokeos185Lp(), new Dokeos185LpItem(), new Dokeos185LPView(), new Dokeos185LpItemView(), new Dokeos185LpIvInteraction(), new Dokeos185LpIvObjective());
	}
}

?>