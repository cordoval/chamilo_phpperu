<?php
namespace application\reservations;

use common\libraries\Utilities;
use common\libraries\WebApplication;

/**
 * $Id: user_autoloader.class.php 167 2009-11-12 11:17:52Z vanpouckesven $
 * @author vanpouckesven
 * @package group
 */

class ReservationsAutoloader
{
	static function load($classname)
	{
            $classname_parts = explode('\\', $classname);

            if (count($classname_parts) == 1)
            {
                return false;
            }
            else
            {
                $classname = $classname_parts[count($classname_parts) - 1];
                array_pop($classname_parts);
                if (implode('\\', $classname_parts) != __NAMESPACE__)
                {
                    return false;
                }
            }

            $list = array(
		'reservation' => 'reservation.class.php',
		'reservations_data_manager' => 'reservations_data_manager.class.php',
		'reservations_data_manager_interface' => 'reservations_data_manager_interface.class.php',
		'reservations_rights' => 'reservations_rights.class.php',
		'reservations_menu' => 'reservations_menu.class.php',
		'subscription_user' => 'subscription_user.class.php',
		'subscription' => 'subscription.class.php',
		'item' => 'item.class.php',
		'quota' => 'quota.class.php',
		'quota_box' => 'quota_box.class.php',
		'quota_rel_quota_box' => 'quota_rel_quota_box.class.php',
		'quota_box_rel_category' => 'quota_box_rel_category.class.php',
                'quota_box_rel_category_rel_user' => 'quota_box_rel_category_rel_user.class.php',
		'quota_box_rel_category_rel_group' => 'quota_box_rel_category_rel_group.class.php',
		'quota_box_rel_category_rel_groupquota_box_rel_category_rel_group' => 'quota_box_rel_category_rel_group.class.php',
		'overview_item' => 'overview_item.class.php',
		'category' => 'category.class.php',
		'reservations_manager' => 'reservations_manager/reservations_manager.class.php',
		'subscription_user_form' => 'forms/subscription_user_form.class.php',
		'subscription_form' => 'forms/subscription_form.class.php',
		'reservation_form' => 'forms/reservation_form.class.php',
		'quota_form' => 'forms/quota_form.class.php',
		'pool_form' => 'forms/pool_form.class.php',
		'quota_box_form' => 'forms/quota_box_form.class.php',
		'item_form' => 'forms/item_form.class.php',
		'overview_item_form' => 'forms/overview_item_form.class.php',
		'category_form' => 'forms/category_form.class.php',
		'category_quota_box_form' => 'forms/category_quota_box_form.class.php',
		'credit_form' => 'forms/credit_form.class.php',
		'reservations_calendar_renderer' => 'calendar/reservations_calendar_renderer.class.php'
            );

            $lower_case = Utilities :: camelcase_to_underscores($classname);

            if (key_exists($lower_case, $list))
            {
                $url = $list[$lower_case];
                require_once WebApplication :: get_application_class_lib_path('reservations') . $url;
                return true;
            }

            return false;
	}
}

?>