<?php
/**
 * $Id: subscribe_user_browser_table_cell_renderer.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package categories.lib.category_manager.component.subscribe_user_browser
 */
require_once dirname(__FILE__) . '/subscribe_user_browser_table_column_model.class.php';
require_once Path :: get_user_path() . 'lib/user_table/default_user_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class SubscribeLocationBrowserTableCellRenderer extends DefaultLocationTableCellRenderer
{
    /**
     * The weblcms browser component
     */
    private $browser;

    /**
     * Constructor
     * @param WeblcmsBrowserComponent $browser
     */
    function SubscribeLocationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $user)
    {
        if ($column === SubscribeLocationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($user);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            case Location :: PROPERTY_STATUS :
 
                if ($user->get_status() == 1)
                {
                    return Translation :: get('CourseAdmin');
                }
                else
                {
                    return Translation :: get('Student');
                }
            case Location :: PROPERTY_PLATFORMADMIN :
                if ($user->get_platformadmin() == '1')
                {
                    return Translation :: get('True');
                }
                else
                {
                    return Translation :: get('False');
                }
            case Location :: PROPERTY_EMAIL :
                return '<a href="mailto:' . $user->get_email() . '">' . $user->get_email() . '</a>';
        }
        return parent :: render_cell($column, $user);
    }

    /**
     * Gets the action links to display
     * @param Location $user The user for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($user)
    {
        $category = $this->browser->get_category();
        $toolbar_data = array();
        
        $subscribe_url = $this->browser->get_category_rel_user_subscribing_url($category, $user);
        $toolbar_data[] = array('href' => $subscribe_url, 'label' => Translation :: get('Subscribe'), 'img' => Theme :: get_common_image_path() . 'action_subscribe.png');
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>