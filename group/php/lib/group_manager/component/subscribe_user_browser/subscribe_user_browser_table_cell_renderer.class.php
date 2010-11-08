<?php
namespace group;
use common\libraries\Translation;
use common\libraries\ToolbarItem; 
use common\libraries\Toolbar; 
use common\libraries\Theme; 
use common\libraries\Path;

use user\DefaultUserTableCellRenderer;
use user\User;
/**
 * $Id: subscribe_user_browser_table_cell_renderer.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package groups.lib.group_manager.component.subscribe_user_browser
 */
require_once dirname(__FILE__) . '/subscribe_user_browser_table_column_model.class.php';
require_once Path :: get_user_path() . 'lib/user_table/default_user_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class SubscribeUserBrowserTableCellRenderer extends DefaultUserTableCellRenderer
{
    /**
     * The weblcms browser component
     */
    private $browser;

    /**
     * Constructor
     * @param WeblcmsBrowserComponent $browser
     */
    function SubscribeUserBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $user)
    {
        if ($column === SubscribeUserBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($user);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            case User :: PROPERTY_STATUS :
 
                if ($user->get_status() == 1)
                {
                    return Translation :: get('CourseAdmin', null , 'user');
                }
                else
                {
                    return Translation :: get('Student', null , 'user');
                }
            case User :: PROPERTY_PLATFORMADMIN :
                if ($user->get_platformadmin() == '1')
                {
                    return Translation :: get('ConfirmTrue', null , Utilities :: COMMON_LIBRARIES);
                }
                else
                {
                    return Translation :: get('ConfirmFalse', null , Utilities :: COMMON_LIBRARIES);
                }
            case User :: PROPERTY_EMAIL :
                return '<a href="mailto:' . $user->get_email() . '">' . $user->get_email() . '</a>';
        }
        return parent :: render_cell($column, $user);
    }

    /**
     * Gets the action links to display
     * @param User $user The user for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($user)
    {
        $group = $this->browser->get_group();

        $toolbar = new Toolbar();
        $toolbar->add_item(new ToolbarItem(
        			Translation :: get('Subscribe'),
        			Theme :: get_common_image_path().'action_subscribe.png', 
					$this->browser->get_group_rel_user_subscribing_url($group, $user),
				 	ToolbarItem :: DISPLAY_ICON
		));
		        
        return $toolbar->as_html();
    }
}
?>