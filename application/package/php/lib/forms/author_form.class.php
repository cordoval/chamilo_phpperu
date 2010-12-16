<?php

namespace application\package;

use common\libraries\FormValidator;
use common\libraries\Translation;
use common\libraries\Path;
use rights\RightsUtilities;
use user\UserDataManager;
use common\libraries\ObjectTableOrder;
use user\User;
use common\libraries\Utilities;
/**
 * This class describes the form for a PackageLanguage object.
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 **/
class AuthorForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
        
    private $package;
    private $user;

    function __construct($form_type, $package, $action, $user)
    {
        parent :: __construct('package_settings', 'post', $action);
        
        $this->package = $package;
        $this->user = $user;
        $this->form_type = $form_type;
        
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_creation_form();
        }
        
        $this->setDefaults();
    }

    function build_basic_form()
    {
        $this->addElement('category', Translation :: get('Properties'));
        $this->addElement('text', Package :: PROPERTY_NAME, Translation :: get('Name'));
        $this->addRule(Package :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        
        $this->addElement('text', Package :: PROPERTY_EMAIL, Translation :: get('Email'));
        $this->addRule(Package :: PROPERTY_EMAIL, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
                
        $this->addElement('text', Package :: PROPERTY_COMPANY, Translation :: get('Company'));
        $this->addRule(Package :: PROPERTY_COMPANY, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
                
        $this->addElement('category');
        //		
        //		$this->addElement('category', Translation :: get('Moderators'));
        //        $url = Path :: get(WEB_PATH) . 'user/php/xml_feeds/xml_user_feed.php';
        //        
        //        $moderators = $this->get_moderator_users();
        //        $defaults = array();
        //        $current = array();
        //        
        //        if ($moderators)
        //        {
        //	        while($moderator = $moderators->next_result())
        //	        {
        //	        	$current[$moderator->get_id()] = array('id' => $moderator->get_id(), 'title' => htmlspecialchars($moderator->get_fullname()), 'description' => htmlspecialchars($moderator->get_username()), 'classes' => 'type type_user');
        //	        	$defaults[$moderator->get_id()] = array('title' => $moderator->get_fullname(), 'description' => $moderator->get_username(), 'class' => 'user');
        //	        }
        //        }
        //        
        //        $locale = array();
        //        $locale['Display'] = Translation :: get('AddModerators');
        //        $locale['Searching'] = Translation :: get('Searching', null, Utilities :: COMMON_LIBRARIES);
        //        $locale['NoResults'] = Translation :: get('NoResults', null, Utilities :: COMMON_LIBRARIES);
        //        $locale['Error'] = Translation :: get('Error', null, Utilities :: COMMON_LIBRARIES);
        //        
        //        $elem = $this->addElement('element_finder', 'moderators', Translation :: get('SelectModerators'), $url, $locale, $current, array('load_elements' => true));
        //		$elem->setDefaults($defaults);
        $this->addElement('category');
    
    }

    function build_editing_form()
    {
        $this->build_basic_form();
        
        //$this->addElement('hidden', PackageLanguage :: PROPERTY_ID);
        

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update', null, Utilities :: COMMON_LIBRARIES), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
        $this->build_basic_form();
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }



    function update_package()
    {
        $package = $this->package;
        $values = $this->exportValues();
        
        $package->set_name($values[Package :: PROPERTY_NAME]);
        $package->set_email($values[Package :: PROPERTY_EMAIL]);
        $package->set_company($values[Package :: PROPERTY_COMPANY]);

        
        if (! $package->update())
        {
            return false;
        }
        //    	$original_moderators = $this->get_moderators();
        //    	$current_moderators = $values['moderators']['user'];
        //    	
        //    	$moderators_to_remove = array_diff($original_moderators, $current_moderators);
        //    	$moderators_to_add = array_diff($current_moderators, $original_moderators);
        //    	
        //    	$location = PackageRights :: get_location_id_by_identifier_from_languages_subtree($package_language->get_table_name(), $package_language->get_id());
        //    	
        //    	foreach ($moderators_to_remove as $moderator)
        //    	{	    		
        //	    	if (!RightsUtilities :: set_user_right_location_value(PackageRights :: EDIT_RIGHT, $moderator, $location, false))
        //	    	{
        //	    		return false;
        //	    	}
        //    	}
        //    	
        //        foreach ($moderators_to_add as $moderator)
        //    	{
        //    		if (!RightsUtilities :: set_user_right_location_value(PackageRights :: EDIT_RIGHT, $moderator, $location, true))
        //	    	{
        //	    		return false;
        //	    	}
        //    	}
        

        return true;
    }

    function create_package()
    {
        $package = $this->package;
        $values = $this->exportValues();
        
        $package->set_name($values[Package :: PROPERTY_NAME]);
        $package->set_email($values[Package :: PROPERTY_EMAIL]);
        $package->set_company($values[Package :: PROPERTY_COMPANY]);
//        dump($package);
        if (! $package->create())
        {
            return false;
        }
        
        //    	$moderators = $values['moderators']['user'];
        //    	$location = PackageRights :: get_location_id_by_identifier_from_languages_subtree($package_language->get_table_name(), $package_language->get_id());
        //    	
        //    	foreach ($moderators as $moderator)
        //    	{	    		
        //	    	if (!RightsUtilities :: set_user_right_location_value(PackageRights :: EDIT_RIGHT, $moderator, $location, true))
        //	    	{
        //	    		return false;
        //	    	}
        //    	}
        

        return true;
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $package = $this->package;
        
        $defaults[Package :: PROPERTY_NAME] = $package->get_name();
        $defaults[Package :: PROPERTY_EMAIL] = $package->get_email();
        $defaults[Package :: PROPERTY_COMPANY] = $package->get_company();

        parent :: setDefaults($defaults);
    }
    
//	function get_moderators()
//	{
//		$package = $this->package;
//		return PackageRights :: get_allowed_users(PackageRights :: EDIT_RIGHT, $package->get_id(), $package->get_table_name());
//	}


//	function get_moderator_users()
//	{
//		$users = $this->get_moderators();
//		
//		if(!empty($users))
//		{
//			$condition = new InCondition(User :: PROPERTY_ID, $users);
//			return UserDataManager :: get_instance()->retrieve_users($condition, null, null, array(new ObjectTableOrder(User :: PROPERTY_LASTNAME), new ObjectTableOrder(User :: PROPERTY_FIRSTNAME)));
//		}
//		else
//		{
//			return null;
//		}
//	}
}
?>