<?php
/**
 * $Id: subscription_form.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.forms
 */
require_once dirname(__FILE__) . '/../subscription.class.php';
require_once dirname(__FILE__) . '/../reservation.class.php';
require_once dirname(__FILE__) . '/../reservations_data_manager.class.php';

class SubscriptionForm extends FormValidator
{
    private $subscription;
    private $reservation;
    private $item;
    private $user;

    function SubscriptionForm($action, $subscription, $reservation, $item, $user)
    {
        parent :: __construct('subscription_form', 'post', $action);

        $this->subscription = $subscription;
        $this->reservation = $reservation;
        $this->item = $item;
        $this->user = $user;

        $this->build_header();

        if ($reservation->get_type() == Reservation :: TYPE_BLOCK)
            $this->build_block_form();
        else
            $this->build_timepicker_form();

        $this->build_footer();

        $this->setDefaults();
    }

    function build_header()
    {
        $this->addElement('html', '<div class="configuration_form">');
        if ($this->reservation->get_type() == Reservation :: TYPE_BLOCK)
            $this->addElement('html', '<span class="category">' . Translation :: get('Information') . '</span>');
        else
            $this->addElement('html', '<span class="category">' . Translation :: get('Required') . '</span>');
    }

    function build_block_form()
    {
        //Please confirm your reservation from <b>%s</b> untill <b>%s</b> for item <b>%s</b>.
        $this->addElement('html', sprintf(Translation :: get('ConfirmSubscription'),
        								  DatetimeUtilities :: format_locale_date(null,$this->reservation->get_start_date()),
        				   	              DatetimeUtilities :: format_locale_date(null,$this->reservation->get_stop_date()), $this->item->get_name()));

        $start = $this->reservation->get_stop_date();
        $rdm = ReservationsDataManager :: get_instance();
        $key = null;

        for($i = 0; $i < 4; $i ++)
        {
            $reservation = $this->get_reservation_block($start);
            if (! $reservation)
                break;

            $conditions = array();
            $conditions[] = new EqualityCondition(Subscription :: PROPERTY_RESERVATION_ID, $reservation->get_id());
            $conditions[] = new EqualityCondition(Subscription :: PROPERTY_STATUS, Subscription :: STATUS_NORMAL);
            $condition = new AndCondition($conditions);

            $subscriptions = $rdm->count_subscriptions($condition);
            if ($subscriptions >= $reservation->get_max_users())
                break;

            $conditions = array();
            $conditions[] = new EqualityCondition(Subscription :: PROPERTY_RESERVATION_ID, $reservation->get_id());
            $conditions[] = new EqualityCondition(Subscription :: PROPERTY_STATUS, Subscription :: STATUS_NORMAL);
            $conditions[] = new EqualityCondition(Subscription :: PROPERTY_USER_ID, $this->user->get_id());
            $condition = new AndCondition($conditions);

            $subscriptions = $rdm->count_subscriptions($condition);

            if ($subscriptions > 0)
                break;

            $start = $reservation->get_stop_date();

            $key = $key . ($key ? ':' : '') . $reservation->get_id();
            $additional_reservations[$key] = $reservation->get_stop_date();
            $this->reservations[$reservation->get_id()] = $reservation;
        }

        if (count($additional_reservations) > 0)
        {
            $additional_reservations[0] = '-- ' . Translation :: get('NoExtension') . ' -- ';
            asort($additional_reservations);
            $this->addElement('html', '<div style="clear: both;"></div>');
            $this->addElement('html', '</div>');
            $this->addElement('html', '<div class="configuration_form">');
            $this->addElement('html', '<span class="category">' . Translation :: get('Optional') . '</span>');
            $this->addElement('select', 'additional_reservations', Translation :: get('ExtendPeriod'), $additional_reservations);
        }

    }

    function get_reservation_block($start_time)
    {
        $rdm = ReservationsDataManager :: get_instance();

        $conditions[] = new EqualityCondition(Reservation :: PROPERTY_ITEM, $this->item->get_id());
        $conditions[] = new EqualityCondition(Reservation :: PROPERTY_STATUS, Subscription :: STATUS_NORMAL);
        $conditions[] = new EqualityCondition(Reservation :: PROPERTY_START_DATE, $start_time);
        $condition = new AndCondition($conditions);

        $reservations = $rdm->retrieve_reservations($condition);

        if ($reservations->size() > 0)
            return $reservations->next_result();
    }

    function build_timepicker_form()
    {
        if ($this->reservation->get_timepicker_max() == 0)
            $this->addElement('html', '<br />' . sprintf(Translation :: get('ChooseTime2'), $this->item->get_name()) . '<br /><ul>');
        else
            $this->addElement('html', '<br />' . sprintf(Translation :: get('ChooseTime'), $this->item->get_name(), $this->reservation->get_timepicker_min(), $this->reservation->get_timepicker_max()) . '<br /><ul>');

        $rdm = ReservationsDataManager :: get_instance();

        $conditions[] = new EqualityCondition(Subscription :: PROPERTY_RESERVATION_ID, $this->reservation->get_id());
        $conditions[] = new EqualityCondition(Subscription :: PROPERTY_STATUS, Subscription :: STATUS_NORMAL);
        $condition = new AndCondition($conditions);

        $subscriptions = $rdm->retrieve_subscriptions($condition);

        while ($subscription = $subscriptions->next_result())
        {
            $times[$subscription->get_start_time()] = $subscription->get_stop_time();
        }

        ksort($times);

        $previous_stop = $start_date = $this->reservation->get_start_date();
        $end_time = $this->reservation->get_stop_date();

        foreach ($times as $start => $stop)
        {
            $previous_stop_time = $previous_stop;
            $start_time = $start;

            if ((($start_time - $previous_stop_time)) > $this->reservation->get_timepicker_min() * 60)
            {
                $this->addElement('html', '<li>' . DatetimeUtilities :: format_locale_date(null, $previous_stop) . ' ' . Translation :: get('and') . ' ' . DatetimeUtilities :: format_locale_date(null, $start) . '</li>');
            }

            $previous_stop = $stop;
        }

        $previous_stop_time = $previous_stop;
        if ((($end_time - $previous_stop_time)) > $this->reservation->get_timepicker_min() * 60)
        {
            $this->addElement('html', '<li>' . DatetimeUtilities :: format_locale_date(null, $previous_stop) . ' ' . Translation :: get('and') . ' ' .
            								   DatetimeUtilities :: format_locale_date(null, $this->reservation->get_stop_date()) . '</li>');
        }

        $this->addElement('html', '</ul>');

        $this->add_timewindow(Subscription :: PROPERTY_START_TIME, Subscription :: PROPERTY_STOP_TIME, Translation :: get('StartDate'), Translation :: get('StopDate'));

        $defaults = array();
        $defaults['start_time'] = $start_date;
        $defaults['stop_time'] = strtotime('+1 Minute', $start_date);
        $this->setDefaults($defaults);
    }

    function build_footer()
    {
        $this->addElement('html', '<div style="clear: both;"></div>');
        $this->addElement('html', '</div>');

        $this->addElement('html', '<div class="configuration_form">');
        $this->addElement('html', '<span class="category">' . Translation :: get('SelectUsers') . '</span>');

        /*$userslist = UserDataManager :: get_instance()->retrieve_users();
    	while($user = $userslist->next_result())
    	{
    		if($user->get_id() != $this->user->get_id())
    			$users[$user->get_id()] = array('title' => $user->get_fullname(), 'description' => $user->get_fullname(), 'class' => 'user');
    	}*/

        //$this->addElement('advmultiselect', 'users', Translation :: get('SelectUsers'),
        //					  $users, array('style' => 'width:200px;'));


        $url = Path :: get(WEB_PATH) . 'user/xml_feeds/xml_user_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('AddUsers');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');

        $elem = $this->addElement('element_finder', 'users', Translation :: get('SelectAdditionalUsers'), $url, $locale, array(), array('load_elements' => false));
        $elem->excludeElements(array($this->user->get_id()));

        $this->addElement('html', '<div style="clear: both;"></div>');
        $this->addElement('html', '</div>');

        // Submit button
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);

    }

    function allow_create_subscription()
    {
        $subscription = $this->subscription;
        if ($this->validate())
        {
            $values = $this->exportValues();
            if ($this->reservation->get_auto_accept() == 1)
                $subscription->set_accepted(1);
            else
                $subscription->set_accepted(0);

            if ($this->reservation->get_type() == Reservation :: TYPE_TIMEPICKER)
            {
                $subscription->set_start_time(Utilities :: time_from_datepicker($values[Subscription :: PROPERTY_START_TIME]));
                $subscription->set_stop_time(Utilities :: time_from_datepicker($values[Subscription :: PROPERTY_STOP_TIME]));

                $start_stamp = $subscription->get_start_time();
                $stop_stamp = $subscription->get_stop_time();
            }
            else
            {
                $start_stamp = $this->reservation->get_start_date();
                $stop_stamp = $this->reservation->get_stop_date();
            }

            $days = ($stop_stamp - $start_stamp) / 3600;
            $needed_credits = $days * $this->item->get_credits();
            $subscription->set_weight($needed_credits);

            $quota_box_id = ReservationsDataManager :: get_instance()->retrieve_quota_box_from_user_for_category($this->user->get_id(), $this->item->get_category());
            $subscription->set_quota_box($quota_box_id);

            return $subscription->allow_create($this->user);
        }
        return 0;
    }

    function create_subscription()
    {
        $subscription = $this->subscription;

        $values = $this->exportValues();

        $optional = $values['additional_reservations'];
        if (isset($optional))
        {
            $subs = $this->make_subscriptions_from_subscription($subscription, $optional);
        }

        //		if($this->item->get_salto_id() != null && $this->item->get_salto_id() != 0)
        //		{
        //			$res = $this->call_webservice($this->user, $subscription);
        //			if(!$res)
        //				return false;
        //
        //			foreach($subs as $sub)
        //			{
        //				$res = $this->call_webservice($this->user, $sub);
        //				if(!$res)
        //					return false;
        //			}
        //		}


        $result = $subscription->create();

        if ($result)
        {
            Event :: trigger('create_subscription', ReservationsManager :: APPLICATION_NAME, array(ChangesTracker :: PROPERTY_REFERENCE_ID => $subscription->get_id(), ChangesTracker :: PROPERTY_USER_ID => $this->user->get_id()));
        }

        foreach ($subs as $sub)
        {
            $res = $sub->create();

            if ($res)
            {
                Event :: trigger('create_subscription', ReservationsManager :: APPLICATION_NAME, array(ChangesTracker :: PROPERTY_REFERENCE_ID => $sub->get_id(), ChangesTracker :: PROPERTY_USER_ID => $this->user->get_id()));
            }

            $result &= $res;
        }

        $users = $this->exportValue('users');
        //	$udm = UserDataManager :: get_instance();


        foreach ($users as $user)
        {
            //			if($this->item->get_salto_id() != null && $this->item->get_salto_id() != 0)
            //			{
            //				$usr = $udm->retrieve_user($user);
            //
            //				$res = $this->call_webservice($usr, $subscription);
            //					if(!$res)
            //						return false;
            //
            //					foreach($subs as $sub)
            //					{
            //						$res = $this->call_webservice($usr, $sub);
            //						if(!$res)
            //							return false;
            //					}
            //			}


            $subscription_user = new SubscriptionUser();
            $subscription_user->set_subscription_id($subscription->get_id());
            $subscription_user->set_user_id($user);
            $result &= $subscription_user->create();

            foreach ($subs as $sub)
            {
                $subscription_user = new SubscriptionUser();
                $subscription_user->set_subscription_id($sub->get_id());
                $subscription_user->set_user_id($user);

                $result &= $subscription_user->create();
            }

        }

        return $result;
    }

    //	function call_webservice($user, $subscription)
    //	{
    //		require_once Path :: get_plugin_path() . 'nusoap/nusoap.php';
    //
    //		$maakreservatieresult = $client->call('MaakReservatie', array(
    //					'sExtUserID' => $user->get_official_code(),
    //					'sExtDoorID' => $this->item->get_salto_id(),
    //					'sTimezoneTableID' => "1"));
    //
    //		$res = $maakreservatieresult['MaakReservatieResult'];
    //
    //		$this->logger->write('Webservice MaakReservatie called (UserID: ' . $user->get_official_code() .
    //				   ', DoorID: ' . $this->item->get_salto_id() . ', TimeZone: ' . "1" . ') Result: ' .
    //				   $res);
    //
    //		return ($res == $usr->get_official_code());
    //	}


    function make_subscriptions_from_subscription($subscription, $additional_reservations)
    {
        $additional_reservations = explode(':', $additional_reservations);

        foreach ($additional_reservations as $reservation_id)
        {
            if ($reservation_id == 0)
                continue;

            $sub = new Subscription();
            $sub->set_default_properties($subscription->get_default_properties());
            $sub->set_reservation_id($reservation_id);
            $subs[] = $sub;
        }

        return $subs;
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $subscription = $this->subscription;
        $defaults[Subscription :: PROPERTY_ID] = $subscription->get_id();

        parent :: setDefaults($defaults);
    }
}
?>