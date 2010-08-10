<?php

require_once dirname(__FILE__) . '/../data_class/dokeos185_blog.class.php';
require_once dirname(__FILE__) . '/../data_class/dokeos185_blog_post.class.php';
require_once dirname(__FILE__) . '/../data_class/dokeos185_blog_comment.class.php';
//require_once dirname(__FILE__) . '/../data_class/dokeos185_blog_attachment.class.php';
require_once dirname(__FILE__) . '/../course_data_migration_block.class.php';

class CourseBlogsMigrationBlock extends CourseDataMigrationBlock
{
	const MIGRATION_BLOCK_NAME = 'course_blogs';
	
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
		return array(new Dokeos185Blog(), new Dokeos185BlogPost(), new Dokeos185BlogComment());
		//return array(new Dokeos185Blog(), new Dokeos185BlogPost(), new Dokeos185BlogComment(), new Dokeos185BlogAttachment());
	}
}

?>