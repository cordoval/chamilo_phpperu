<?php

require_once dirname(__FILE__) . '/../data_class/dokeos185_forum_category.class.php';
require_once dirname(__FILE__) . '/../data_class/dokeos185_forum_forum.class.php';
require_once dirname(__FILE__) . '/../data_class/dokeos185_forum_thread.class.php';
require_once dirname(__FILE__) . '/../data_class/dokeos185_forum_post.class.php';
require_once dirname(__FILE__) . '/../course_data_migration_block.class.php';

class CourseForumsMigrationBlock extends CourseDataMigrationBlock
{
	const MIGRATION_BLOCK_NAME = 'course_forums';
	
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
		return array(new Dokeos185ForumCategory(), new Dokeos185ForumForum(), new Dokeos185ForumThread(), new Dokeos185ForumPost());
	}
}

?>