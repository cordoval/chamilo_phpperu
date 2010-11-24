<?php

namespace application\reservations;

use common\libraries\WebApplication;
/**
 * $Id: subscription_user_browser_table_column_model.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component.subscription_user_browser
 */
require_once WebApplication :: get_application_class_lib_path('reservations') . 'tables/subscription_user_table/default_subscription_user_table_column_model.class.php';

/**
 * Table column model for the user browser table
 */
class SubscriptionUserBrowserTableColumnModel extends DefaultSubscriptionUserTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function __construct($browser)
    {
        parent :: __construct($browser);
        $this->set_default_order_column(1);
    }

}
?>