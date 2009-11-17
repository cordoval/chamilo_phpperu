<?php
/**
 * $Id: default_subscription_user_table_cell_renderer.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.tables.subscription_user_table
 */

require_once dirname(__FILE__) . '/../../subscription_user.class.php';
/**
 * TODO: Add comment
 */
class DefaultSubscriptionUserTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultSubscriptionUserTableCellRenderer($browser)
    {
    
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $subscription_user)
    {
        if ($property = $column->get_name())
        {
            switch ($property)
            {
                case SubscriptionUser :: PROPERTY_USER_ID :
                    $user = UserDataManager :: get_instance()->retrieve_user($subscription_user->get_user_id());
                    return $user->get_fullname();
            }
        
        }
        
        return '&nbsp;';
    }

    function render_id_cell($subscription_user)
    {
        return $subscription_user->get_user_id();
    }
}
?>