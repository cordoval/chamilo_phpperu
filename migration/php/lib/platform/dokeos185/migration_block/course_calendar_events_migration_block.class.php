<?php
namespace migration;

require_once dirname(__FILE__) . '/course_groups_migration_block.class.php';
require_once dirname(__FILE__) . '/../data_class/dokeos185_calendar_event.class.php';
require_once dirname(__FILE__) . '/../course_data_migration_block.class.php';

class CourseCalendarEventsMigrationBlock extends CourseDataMigrationBlock
{
    const MIGRATION_BLOCK_NAME = 'course_calendar_events';

    function get_prerequisites()
    {
        return array(CoursesMigrationBlock :: MIGRATION_BLOCK_NAME, CourseGroupsMigrationBlock :: MIGRATION_BLOCK_NAME);
    }

    function get_block_name()
    {
        return self :: MIGRATION_BLOCK_NAME;
    }

    function get_course_data_classes()
    {
        return array(new Dokeos185CalendarEvent());
    }

}

?>