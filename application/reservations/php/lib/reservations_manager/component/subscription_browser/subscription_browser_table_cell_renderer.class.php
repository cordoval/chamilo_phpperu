<?php

namespace application\reservations;

use common\libraries\WebApplication;
use common\libraries\Toolbar;
use common\libraries\ToolbarItem;
use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\Utilities;
/**
 * $Id: subscription_browser_table_cell_renderer.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component.subscription_browser
 */
require_once WebApplication :: get_application_class_lib_path('reservations') . 'reservations_manager/component/subscription_browser/subscription_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('reservations') . 'tables/subscription_table/default_subscription_table_cell_renderer.class.php';

/**
 * Cell rendere for the learning object browser table
 */
class SubscriptionBrowserTableCellRenderer extends DefaultSubscriptionTableCellRenderer
{
    /**
     * The repository browser component
     */
    protected $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function SubscriptionBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $subscription)
    {
        if ($column === SubscriptionBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($subscription);
        }
        
        return parent :: render_cell($column, $subscription);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($subscription)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Delete'),
        		Theme :: get_common_image_path() . 'action_delete.png',
        		$this->browser->get_delete_subscription_url($subscription->get_id()),
        		ToolbarItem :: DISPLAY_ICON,
        		true
        ));
        
     	if (Utilities :: get_classname_from_namespace(get_class($this->browser)) == 'ReservationsManagerAdminSubscriptionBrowserComponent' && $subscription->get_accepted() == 0)
        {
            $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('Accept'),
	        		Theme :: get_common_image_path() . 'thumbs_up.png',
	        		$this->browser->get_approve_subscription_url($subscription->get_id()),
	        		ToolbarItem :: DISPLAY_ICON
	        ));
        }
        
        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Details'),
        		Theme :: get_common_image_path() . 'action_browser.png',
        		 $this->browser->get_subscription_user_browser_url($subscription->get_id()),
        		ToolbarItem :: DISPLAY_ICON
        ));
        
        return $toolbar->as_html();
    }
}
?>