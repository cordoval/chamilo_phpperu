<?php
/**
 * @author Hans De Bisschop
 */

class InvitationManager extends SubManager
{
    const CLASS_NAME = __CLASS__;

    const PARAM_INVITATION_CODE = 'invitation_code';

    function InvitationManager($application)
    {
        parent :: __construct($application);
    }

    function run()
    {

    }

	function get_application_component_path()
	{
		return Path :: get_application_library_path() . 'invitation_manager/component/';
	}

	/**
	 * @param FormValidator $form
	 * @param boolean $show_message_field
	 */
	static function get_elements(FormValidator $form, $show_message_fields = true)
	{
	    $form->addElement('category', Translation :: get('InviteExternalUsers'));
	    $form->add_information_message(null, null, Translation :: get('CommaSeparatedListOfEmailAddresses'));
	    $form->addElement('textarea', Invitation :: PROPERTY_EMAIL, Translation :: get('EmailAddresses'), 'cols="70" rows="8"');
	    $form->addRule(Invitation :: PROPERTY_EMAIL, Translation :: get('ThisFieldIsRequired'), 'required');
	    $form->add_forever_or_expiration_date_window(Invitation :: PROPERTY_EXPIRATION_DATE);

        $rights_templates = RightsDataManager :: get_instance()->retrieve_rights_templates();
        while ($rights_template = $rights_templates->next_result())
        {
            $defaults[$rights_template->get_id()] = array('title' => $rights_template->get_name(), 'description', $rights_template->get_description(), 'class' => 'rights_template');
        }

        $url = Path :: get(WEB_PATH) . 'rights/xml_feeds/xml_rights_template_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('AddRightsTemplates');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $hidden = true;

        $elem = $form->addElement('element_finder', 'rights_templates', null, $url, $locale, array());
        $elem->setDefaultCollapsed(true);

        if ($show_message_fields)
        {
            $form->addElement('text', Invitation :: PROPERTY_TITLE, Translation :: get('Subject'));
            $form->addRule(Invitation :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');
            $form->add_html_editor(Invitation :: PROPERTY_MESSAGE, Translation :: get('Message'), true);
        }

	    $form->addElement('category');
	    $form->setDefaults(array('forever'=> 1));
	}
}
?>