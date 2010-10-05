<?php
/**
 * $Id: course_group_unsubscribed_user_browser_table_column_model.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_group.component.user_table
 */

require_once Path :: get_user_path() . 'lib/user_table/default_user_table_column_model.class.php';

class CourseGroupUnsubscribedUserBrowserTableColumnModel extends DefaultUserTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function CourseGroupUnsubscribedUserBrowserTableColumnModel()
    {
        parent :: __construct();
        $this->add_column(new ObjectTableColumn(User :: PROPERTY_USERNAME));
        $this->add_column(new ObjectTableColumn(User :: PROPERTY_EMAIL));
        $this->set_default_order_column(1);
        $this->add_column(self :: get_modification_column());
    }

    /**
     * Gets the modification column
     * @return ContentObjectTableColumn
     */
    static function get_modification_column()
    {
        if (! isset(self :: $modification_column))
        {
            self :: $modification_column = new StaticTableColumn('');
        }
        return self :: $modification_column;
    }
}
?>