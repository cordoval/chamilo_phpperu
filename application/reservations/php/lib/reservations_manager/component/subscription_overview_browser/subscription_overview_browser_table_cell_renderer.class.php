<?php
/**
 * $Id: subscription_overview_browser_table_cell_renderer.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component.subscription_overview_browser
 */
require_once dirname(__FILE__) . '/subscription_overview_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/subscription_table/default_subscription_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../subscription.class.php';
require_once dirname(__FILE__) . '/../../reservations_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class SubscriptionOverviewBrowserTableCellRenderer extends DefaultSubscriptionTableCellRenderer
{
    /**
     * The repository browser component
     */
    protected $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function SubscriptionOverviewBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $subscription)
    {
        if (! $this->reservation || $this->reservation->get_id() != $subscription->get_reservation_id())
        {
            $this->reservation = $this->browser->retrieve_reservations(new EqualityCondition(Reservation :: PROPERTY_ID, $subscription->get_reservation_id()))->next_result();
        }
        
        if ($property = $column->get_name())
        {
            switch ($property)
            {
                case Item :: PROPERTY_NAME :
                    $item = $this->browser->retrieve_items(new EqualityCondition(Item :: PROPERTY_ID, $this->reservation->get_item()))->next_result();
                    return $item->get_name();
                case User :: PROPERTY_FIRSTNAME :
                    $user = UserDataManager :: get_instance()->retrieve_user($subscription->get_user_id());
                    return $user->get_fullname();
                case 'AdditionalUsers' :
                    $additional_users = $this->browser->retrieve_subscription_users(new EqualityCondition(SubscriptionUser :: PROPERTY_SUBSCRIPTION_ID, $subscription->get_id()));
                    $size = $additional_users->size();
                    
                    $title = '';
                    
                    while ($add_user = $additional_users->next_result())
                    {
                        $user = UserDataManager :: get_instance()->retrieve_user($add_user->get_user_id());
                        $title .= $user->get_fullname() . "\n";
                    }
                    
                    return '<div style="width: 100%;" title="' . $title . '">' . $size . '</div>';
            }
        }
        
        return parent :: render_cell($column, $subscription);
    }
}
?>