<?php
/**
 * $Id: whois_online_table_column_model.class.php 166 2009-11-12 11:03:06Z vanpouckesven $
 * @package admin.lib.admin_manager.component.whois_online_table
 */
require_once Path :: get_user_path() . 'lib/user_table/default_user_table_column_model.class.php';
/**
 * Table column model for the user browser table
 */
class WhoisOnlineTableColumnModel extends DefaultUserTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function WhoisOnlineTableColumnModel()
    {
        parent :: __construct();
        $this->add_column(new ObjectTableColumn(User :: PROPERTY_EMAIL));
        $this->add_column(new ObjectTableColumn(User :: PROPERTY_STATUS));
        $this->add_column(new ObjectTableColumn(User :: PROPERTY_PICTURE_URI));
        //$this->add_column(new ObjectTableColumn(User :: PROPERTY_PLATFORMADMIN));
        $this->set_default_order_column(1);
    }
}
?>