<?php
require_once dirname(__FILE__) . '/../cda_language.class.php';

/**
 * This class describes the form for a CdaLanguage object.
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 **/
class CdaLanguageForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $cda_language;
	private $user;

    function CdaLanguageForm($form_type, $cda_language, $action, $user)
    {
    	parent :: __construct('cda_language_settings', 'post', $action);

    	$this->cda_language = $cda_language;
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
		$this->addElement('text', CdaLanguage :: PROPERTY_ORIGINAL_NAME, Translation :: get('OriginalName'));
		$this->addRule(CdaLanguage :: PROPERTY_ORIGINAL_NAME, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', CdaLanguage :: PROPERTY_ENGLISH_NAME, Translation :: get('EnglishName'));
		$this->addRule(CdaLanguage :: PROPERTY_ENGLISH_NAME, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', CdaLanguage :: PROPERTY_ISOCODE, Translation :: get('Isocode'));
		$this->addRule(CdaLanguage :: PROPERTY_ISOCODE, Translation :: get('ThisFieldIsRequired'), 'required');
		
		$this->addElement('checkbox', CdaLanguage :: PROPERTY_RTL, Translation :: get('RightToLeft'));
		
		$this->addElement('category');
		
		$this->addElement('category', Translation :: get('Moderators'));
        $url = Path :: get(WEB_PATH) . 'user/xml_feeds/xml_user_feed.php';
        
        $moderators = $this->get_moderator_users();
        $defaults = array();
        $current = array();
        
        if ($moderators)
        {
	        while($moderator = $moderators->next_result())
	        {
	        	$current[$moderator->get_id()] = array('id' => $moderator->get_id(), 'title' => htmlspecialchars($moderator->get_fullname()), 'description' => htmlspecialchars($moderator->get_username()), 'classes' => 'type type_user');
	        	$defaults[$moderator->get_id()] = array('title' => $moderator->get_fullname(), 'description' => $moderator->get_username(), 'class' => 'user');
	        }
        }
        
        $locale = array();
        $locale['Display'] = Translation :: get('AddModerators');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        
        $elem = $this->addElement('element_finder', 'moderators', Translation :: get('SelectModerators'), $url, $locale, $current, array('load_elements' => true));
		$elem->setDefaults($defaults);
		$this->addElement('category');

    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	//$this->addElement('hidden', CdaLanguage :: PROPERTY_ID);

		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
    	$this->build_basic_form();

		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_cda_language()
    {
    	$cda_language = $this->cda_language;
    	$values = $this->exportValues();
    	
    	$cda_language->set_original_name($values[CdaLanguage :: PROPERTY_ORIGINAL_NAME]);
    	$cda_language->set_english_name($values[CdaLanguage :: PROPERTY_ENGLISH_NAME]);
    	$cda_language->set_isocode($values[CdaLanguage :: PROPERTY_ISOCODE]);

    	if($values[CdaLanguage :: PROPERTY_RTL])
    	{
    		$cda_language->set_rtl(1);
    	}
    	else
    	{
    		$cda_language->set_rtl(0);
    	}
    	
    	if (!$cda_language->update())
    	{
    		return false;
    	}
    	
    	$original_moderators = $this->get_moderators();
    	$current_moderators = $values['moderators']['user'];
    	
    	$moderators_to_remove = array_diff($original_moderators, $current_moderators);
    	$moderators_to_add = array_diff($current_moderators, $original_moderators);
    	
    	$location = CdaRights :: get_location_id_by_identifier_from_languages_subtree($cda_language->get_table_name(), $cda_language->get_id());
    	
    	foreach ($moderators_to_remove as $moderator)
    	{	    		
	    	if (!RightsUtilities :: set_user_right_location_value(CdaRights :: EDIT_RIGHT, $moderator, $location, false))
	    	{
	    		return false;
	    	}
    	}
    	
        foreach ($moderators_to_add as $moderator)
    	{
    		if (!RightsUtilities :: set_user_right_location_value(CdaRights :: EDIT_RIGHT, $moderator, $location, true))
	    	{
	    		return false;
	    	}
    	}
    	
    	return true;
    }

    function create_cda_language()
    {
    	$cda_language = $this->cda_language;
    	$values = $this->exportValues();
    	
    	$cda_language->set_original_name($values[CdaLanguage :: PROPERTY_ORIGINAL_NAME]);
    	$cda_language->set_english_name($values[CdaLanguage :: PROPERTY_ENGLISH_NAME]);
    	$cda_language->set_isocode($values[CdaLanguage :: PROPERTY_ISOCODE]);
   		
    	if($values[CdaLanguage :: PROPERTY_RTL])
    	{
    		$cda_language->set_rtl(1);
    	}
    	else
    	{
    		$cda_language->set_rtl(0);
    	}
    	
    	if (!$cda_language->create())
    	{
    		return false;
    	}
    	
    	$moderators = $values['moderators']['user'];
    	$location = CdaRights :: get_location_id_by_identifier_from_languages_subtree($cda_language->get_table_name(), $cda_language->get_id());
    	
    	foreach ($moderators as $moderator)
    	{	    		
	    	if (!RightsUtilities :: set_user_right_location_value(CdaRights :: EDIT_RIGHT, $moderator, $location, true))
	    	{
	    		return false;
	    	}
    	}
    	
    	return true;
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$cda_language = $this->cda_language;

    	$defaults[CdaLanguage :: PROPERTY_ORIGINAL_NAME] = $cda_language->get_original_name();
    	$defaults[CdaLanguage :: PROPERTY_ENGLISH_NAME] = $cda_language->get_english_name();
    	$defaults[CdaLanguage :: PROPERTY_ISOCODE] = $cda_language->get_isocode();

		parent :: setDefaults($defaults);
	}
	
	function get_moderators()
	{
		$cda_language = $this->cda_language;
		return CdaRights :: get_allowed_users(CdaRights :: EDIT_RIGHT, $cda_language->get_id(), $cda_language->get_table_name());
	}
	
	function get_moderator_users()
	{
		$users = $this->get_moderators();
		
		if(!empty($users))
		{
			$condition = new InCondition(User :: PROPERTY_ID, $users);
			return UserDataManager :: get_instance()->retrieve_users($condition, null, null, array(new ObjectTableOrder(User :: PROPERTY_LASTNAME), new ObjectTableOrder(User :: PROPERTY_FIRSTNAME)));
		}
		else
		{
			return null;
		}
	}
}
?>