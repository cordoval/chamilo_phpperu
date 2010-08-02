<?php
/**
 * $Id: subscription_user_browser_table_cell_renderer.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component.subscription_user_browser
 */
require_once dirname(__FILE__) . '/subscription_user_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/subscription_user_table/default_subscription_user_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../subscription_user.class.php';
require_once dirname(__FILE__) . '/../../reservations_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class SubscriptionUserBrowserTableCellRenderer extends DefaultSubscriptionUserTableCellRenderer
{
    /**
     * The repository browser component
     */
    protected $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function SubscriptionUserBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $subscription_user)
    {
        return parent :: render_cell($column, $subscription_user);
    }
    
    function render_id_cell($subscription_user)
    {
    	return $subscription_user->get_user_id();
    }
}
?>