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
        parent :: __construct('author_settings', 'post', $action);
        
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
        $this->addElement('text', Author :: PROPERTY_NAME, Translation :: get('Name'));
        $this->addRule(Author :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        
        $this->addElement('text', Author :: PROPERTY_EMAIL, Translation :: get('Email'));
        $this->addRule(Author :: PROPERTY_EMAIL, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
                
        $this->addElement('text', Author :: PROPERTY_COMPANY, Translation :: get('Company'));
        $this->addRule(Author :: PROPERTY_COMPANY, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
                
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



    function update_author()
    {
        $package = $this->package;
        $values = $this->exportValues();
        
        $package->set_name($values[Author :: PROPERTY_NAME]);
        $package->set_email($values[Author :: PROPERTY_EMAIL]);
        $package->set_company($values[Author :: PROPERTY_COMPANY]);

        
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

    function create_author()
    {
        $package = $this->package;
        $values = $this->exportValues();
        
        $package->set_name($values[Author :: PROPERTY_NAME]);
        $package->set_email($values[Author :: PROPERTY_EMAIL]);
        $package->set_company($values[Author :: PROPERTY_COMPANY]);
//        dump($package);
        if (! $package->create())
        {
            return false;
        }
        
        return true;
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $package = $this->package;
        
        $defaults[Author :: PROPERTY_NAME] = $package->get_name();
        $defaults[Author :: PROPERTY_EMAIL] = $package->get_email();
        $defaults[Author :: PROPERTY_COMPANY] = $package->get_company();

        parent :: setDefaults($defaults);
    }
}
?>