<?php
/**
 * $Id: subscription_user_form.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.forms
 */
require_once dirname(__FILE__) . '/../reservations_data_manager.class.php';

class SubscriptionUserForm extends FormValidator
{
    private $subscription;
    private $user;
    private $reservation;
    private $item;

    function SubscriptionUserForm($action, $subscription, $user, $reservation, $item)
    {
        parent :: __construct('subscription_user_form', 'post', $action);
        
        $this->subscription = $subscription;
        $this->user = $user;
        $this->item = $item;
        $this->reservation = $reservation;
        
        $this->build_form();
        $this->setDefaults();
    }

    function build_form()
    {
        $this->addElement('html', '<div class="configuration_form">');
        $this->addElement('html', '<span class="category">' . Translation :: get('SelectAdditionalUsers') . '</span>');
        
        $subscription = $this->subscription;
        
        $condition = new EqualityCondition(SubscriptionUser :: PROPERTY_SUBSCRIPTION_ID, $subscription->get_id());
        $selected_users = ReservationsDataManager :: get_instance()->retrieve_subscription_users($condition);
        while ($selected_user = $selected_users->next_result())
        {
            $user = UserDataManager :: get_instance()->retrieve_user($selected_user->get_user_id());
            $user_list[$user->get_id()] = array('id' => htmlentities($user->get_id(), ENT_COMPAT, 'UTF-8'), 'title' => htmlentities($user->get_fullname(), ENT_COMPAT, 'UTF-8'), 'description' => $user->get_fullname(), 'class' => 'user');
        }
        
        $url = Path :: get(WEB_PATH) . 'user/xml_feeds/xml_user_feed.php';
        
        //$this->addElement('advmultiselect', 'users', Translation :: get('SelectUsers'), 
        //			  $users, array('style' => 'width:200px;'));
        

        $locale = array();
        $locale['Display'] = Translation :: get('AddUsers');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        
        $elem = $this->addElement('element_finder', 'users', null, $url, $locale, $user_list, array('load_elements' => false));
        $elem->excludeElements(array($this->user->get_id()));
        
        $this->addElement('html', '<div style="clear: both;"></div>');
        $this->addElement('html', '</div>');
        
        // Submit button
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_subscription_users()
    {
        $result = true;
        
        $subscription = $this->subscription;
        $export_users = $this->exportValue('users');
        
        $condition = new EqualityCondition(SubscriptionUser :: PROPERTY_SUBSCRIPTION_ID, $subscription->get_id());
        $users = ReservationsDataManager :: get_instance()->retrieve_subscription_users($condition);
        while ($user = $users->next_result())
        {
            if (($search = array_search($user->get_user_id(), $export_users)) === false)
            {
                $user->delete();
            }
            else
            {
                unset($export_users[$search]);
            }
        }
        
        if ($this->item->get_salto_id() != null && $this->item->get_salto_id() != 0)
        {
            require_once Path :: get_plugin_path() . 'nusoap/nusoap.php';
        }
        
        //		$udm = UserDataManager :: get_instance();
        

        foreach ($export_users as $user)
        {
            $subscription_user = new SubscriptionUser();
            $subscription_user->set_subscription_id($subscription->get_id());
            $subscription_user->set_user_id($user);
            //			
            //			$usr = $udm->retrieve_user($user);
            //			
            //			if($this->item->get_salto_id() != null && $this->item->get_salto_id() != 0)
            //			{
            //				$maakreservatieresult = $client->call('MaakReservatie', array(
            //				'sExtUserID' => $usr->get_official_code(), 
            //				'sExtDoorID' => $this->item->get_salto_id(), 
            //				'sTimezoneTableID' => "1"));
            //				
            //				$res = $maakreservatieresult['MaakReservatieResult'];
            //			
            //				$logger->write('Webservice MaakReservatie called (UserID: ' . $usr->get_official_code() .
            //						   ', DoorID: ' . $this->item->get_salto_id() . ', TimeZone: ' . "1" . ') Result: ' .
            //						   $res);
            //			
            //				if($res != $usr->get_official_code())
            //					continue;
            //			}
            

            $result &= $subscription_user->create();
        }
        
        return $result;
    }
}
?>