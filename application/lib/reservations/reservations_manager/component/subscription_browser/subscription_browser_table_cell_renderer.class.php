<?php
/**
 * $Id: subscription_browser_table_cell_renderer.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component.subscription_browser
 */
require_once dirname(__FILE__) . '/subscription_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/subscription_table/default_subscription_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../subscription.class.php';
require_once dirname(__FILE__) . '/../../reservations_manager.class.php';
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
        $toolbar_data = array();
        
        $toolbar_data[] = array('href' => $this->browser->get_delete_subscription_url($subscription->get_id()), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png', 'confirm' => true);
        
        if (get_class($this->browser) == 'ReservationsManagerAdminSubscriptionBrowserComponent' && $subscription->get_accepted() == 0)
        {
            $toolbar_data[] = array('href' => $this->browser->get_approve_subscription_url($subscription->get_id()), 'label' => Translation :: get('Accept'), 'img' => Theme :: get_common_image_path() . 'thumbs_up.png');
        }
        
        $toolbar_data[] = array('href' => $this->browser->get_subscription_user_browser_url($subscription->get_id()), 'label' => Translation :: get('Details'), 'img' => Theme :: get_common_image_path() . 'action_browser.png');
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>