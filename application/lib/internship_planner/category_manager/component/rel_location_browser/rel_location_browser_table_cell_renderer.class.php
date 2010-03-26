<?php
/**
 * $Id: category_rel_user_browser_table_cell_renderer.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package categories.lib.category_manager.component.category_rel_user_browser
 */
require_once dirname(__FILE__) . '/category_rel_user_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../category_rel_user_table/default_category_rel_user_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class InternshipPlannerCategoryRelLocationBrowserTableCellRenderer extends DefaultInternshipPlannerCategoryRelLocationTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function InternshipPlannerCategoryRelLocationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $categoryreluser)
    {
        if ($column === InternshipPlannerCategoryRelLocationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($categoryreluser);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            case InternshipPlannerCategoryRelLocation :: PROPERTY_LOCATION_ID :
                $user_id = parent :: render_cell($column, $categoryreluser);
                $user = LocationManager :: retrieve_user($user_id);
                //				return '<a href="' . Path :: get(WEB_PATH) . 'index_user.php?go=view&id=' . $user->get_id() .
                //					'">' . $user->get_username() . '</a>';
                return $user->get_fullname();
        }
        return parent :: render_cell($column, $categoryreluser);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($categoryreluser)
    {
        $toolbar_data = array();
        
        $toolbar_data[] = array('href' => $this->browser->get_category_rel_user_unsubscribing_url($categoryreluser), 'label' => Translation :: get('Unsubscribe'), 'img' => Theme :: get_common_image_path() . 'action_delete.png');
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>