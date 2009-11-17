<?php
/**
 * $Id: location_browser_table_cell_renderer.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.location_manager.component.location_browser_table
 */
require_once dirname(__FILE__) . '/location_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/location_table/default_location_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class LocationBrowserTableCellRenderer extends DefaultLocationTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function LocationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $location)
    {
        if ($column === LocationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($location);
        }
        
        switch ($column->get_name())
        {
            case Location :: PROPERTY_LOCATION :
                if ($location->has_children())
                {
                    return '<a href="' . htmlentities($this->browser->get_url(array(LocationManager :: PARAM_SOURCE => $location->get_application(), LocationManager :: PARAM_LOCATION => $location->get_id()))) . '">' . parent :: render_cell($column, $location) . '</a>';
                }
                else
                {
                    return parent :: render_cell($column, $location);
                }
                break;
            case Location :: PROPERTY_LOCKED :
                if ($location->is_locked())
                {
                    return '<a href="' . $this->browser->get_location_unlocking_url($location) . '"><img src="' . htmlentities(Theme :: get_common_image_path() . 'action_lock.png') . '" alt="' . Translation :: get('Locked') . '" title="' . Translation :: get('Locked') . '" /></a>';
                }
                else
                {
                    return '<a href="' . $this->browser->get_location_locking_url($location) . '"><img src="' . htmlentities(Theme :: get_common_image_path() . 'action_unlock.png') . '" alt="' . Translation :: get('Unlocked') . '" title="' . Translation :: get('Unlocked') . '" /></a>';
                }
                break;
            case Location :: PROPERTY_INHERIT :
                if ($location->inherits())
                {
                    return '<a href="' . $this->browser->get_location_disinheriting_url($location) . '"><img src="' . htmlentities(Theme :: get_common_image_path() . 'action_setting_true_inherit.png') . '" alt="' . Translation :: get('Inherits') . '" title="' . Translation :: get('Inherits') . '" /></a>';
                }
                else
                {
                    return '<a href="' . $this->browser->get_location_inheriting_url($location) . '"><img src="' . htmlentities(Theme :: get_common_image_path() . 'action_setting_false_inherit.png') . '" alt="' . Translation :: get('DoesNotInherit') . '" title="' . Translation :: get('DoesNotInherit') . '" /></a>';
                }
                break;
        }
        
        return parent :: render_cell($column, $location);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($location)
    {
        $toolbar_data = array();
        
        //		$reset_url = $this->browser->get_location_reset_url($location);
        $toolbar_data[] = array(//			'href' => $reset_url,
        'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_reset.png');
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>