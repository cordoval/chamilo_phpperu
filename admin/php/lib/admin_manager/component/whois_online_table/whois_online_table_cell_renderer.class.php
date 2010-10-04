<?php
/**
 * $Id: whois_online_table_cell_renderer.class.php 166 2009-11-12 11:03:06Z vanpouckesven $
 * @package admin.lib.admin_manager.component.whois_online_table
 */
require_once dirname(__FILE__) . '/whois_online_table_column_model.class.php';
require_once Path :: get_user_path() . 'lib/user_table/default_user_table_cell_renderer.class.php';
/**
 * Cell renderer for the user object browser table
 */
class WhoisOnlineTableCellRenderer extends DefaultUserTableCellRenderer
{
    /**
     * The user browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function WhoisOnlineTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $user)
    {
        // Add special features here
        switch ($column->get_name())
        {
            case User :: PROPERTY_OFFICIAL_CODE :
                return $user->get_official_code();
            // Exceptions that need post-processing go here ...
            case User :: PROPERTY_STATUS :
                if ($user->get_platformadmin() == '1')
                {
                    return Translation :: get('PlatformAdministrator');
                }
                if ($user->get_status() == '1')
                {
                    return Translation :: get('CourseAdmin');
                }
                else
                {
                    return Translation :: get('Student');
                }
            case User :: PROPERTY_PLATFORMADMIN :
                if ($user->get_platformadmin() == '1')
                {
                    return Translation :: get('PlatformAdministrator');
                }
                else
                {
                    return '';
                }
            case User :: PROPERTY_PICTURE_URI :
                if ($user->get_picture_uri())
                {
                    return '<a href="' . $this->browser->get_url(array('uid' => $user->get_id())) . '">' . '<img style="max-width: 100px; max-height: 100px;" src="' . $user->get_full_picture_url() . '" alt="' . Translation :: get('UserPic') . '" /></a>';
                }
                return '';
        }
        return parent :: render_cell($column, $user);
    }

}
?>