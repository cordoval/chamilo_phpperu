<?php
class InvitationForm extends FormValidator
{
    private $valid_email_regex = '/^((\"[^\"\f\n\r\t\v\b]+\")|([\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+(\.[\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+)*))@((\[(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))\])|(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))|((([A-Za-z0-9\-])+\.)+[A-Za-z\-]+))$/';

    function InvitationForm($action)
    {
        parent :: __construct('invitation_form', 'post', $action);

        $this->build_form();
        $this->setDefaults();
    }

    function build_form()
    {
        $this->addElement('category', Translation :: get('Invitation'));

        $this->add_information_message(null, null, Translation :: get('CommaSeparatedListOfEmailAddresses'));
        $this->addElement('textarea', Invitation :: PROPERTY_EMAIL, Translation :: get('EmailAddresses'), 'cols="70" rows="8"');
        $this->addRule(Invitation :: PROPERTY_EMAIL, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->add_forever_or_expiration_date_window(Invitation :: PROPERTY_EXPIRATION_DATE);
        $this->addElement('checkbox', Invitation :: PROPERTY_ANONYMOUS, Translation :: get('Anonymous'), null, 1);

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

        $element_finder = $this->addElement('element_finder', 'rights_templates', null, $url, $locale, array());
        $element_finder->setDefaultCollapsed(true);

        $this->addElement('category');

        $this->addElement('category', Translation :: get('InvitationMessage'));
        $this->add_textfield(Invitation :: PROPERTY_TITLE, Translation::get('InvitationSubject'), true);
        //$this->addElement('text', Invitation :: PROPERTY_TITLE, Translation :: get('InvitationSubject'));
        $this->addRule(Invitation :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->add_html_editor(Invitation :: PROPERTY_MESSAGE, Translation :: get('InvitationBody'), true);
        $this->addElement('category');

        $checkboxes = array();
        $checkboxes[] = '<script type="text/javascript">';
        $checkboxes[] = '$(document).ready(function() {';
        $checkboxes[] = '$("input:checkbox[name=\'' . Invitation :: PROPERTY_ANONYMOUS . '\']").iphoneStyle({ checkedLabel: \'' . Translation :: get('Yes') . '\', uncheckedLabel: \'' . Translation :: get('No') . '\'});';
        $checkboxes[] = '});';
        $checkboxes[] = '</script>';
        $this->addElement('html', implode("\n", $checkboxes));

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Invite'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $defaults['forever'] = 0;
        parent :: setDefaults($defaults);
    }

//    function render()
//    {
//        $this->form->addElement('category', Translation :: get('InviteExternalUsers'));
//        $this->form->add_information_message(null, null, Translation :: get('CommaSeparatedListOfEmailAddresses'));
//        $this->form->addElement('textarea', Invitation :: PROPERTY_EMAIL, Translation :: get('EmailAddresses'), 'cols="70" rows="8"');
//        $this->form->addRule(Invitation :: PROPERTY_EMAIL, Translation :: get('ThisFieldIsRequired'), 'required');
//        $this->form->add_forever_or_expiration_date_window(Invitation :: PROPERTY_EXPIRATION_DATE);
//        $this->form->addElement('checkbox', Invitation :: PROPERTY_ANONYMOUS, Translation :: get('Anonymous'), null, 1);
//
//        $rights_templates = RightsDataManager :: get_instance()->retrieve_rights_templates();
//        while ($rights_template = $rights_templates->next_result())
//        {
//            $defaults[$rights_template->get_id()] = array('title' => $rights_template->get_name(), 'description', $rights_template->get_description(), 'class' => 'rights_template');
//        }
//
//        $url = Path :: get(WEB_PATH) . 'rights/xml_feeds/xml_rights_template_feed.php';
//        $locale = array();
//        $locale['Display'] = Translation :: get('AddRightsTemplates');
//        $locale['Searching'] = Translation :: get('Searching');
//        $locale['NoResults'] = Translation :: get('NoResults');
//        $locale['Error'] = Translation :: get('Error');
//        $hidden = true;
//
//        //        $element_finder = $this->form->addElement('element_finder', 'rights_templates', null, $url, $locale, array());
//        //        $element_finder->setDefaultCollapsed(true);
//
//
//        if ($this->show_message_fields)
//        {
//            $this->form->addElement('text', Invitation :: PROPERTY_TITLE, Translation :: get('Subject'));
//            $this->form->addRule(Invitation :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');
//            $this->form->add_html_editor(Invitation :: PROPERTY_MESSAGE, Translation :: get('Message'), true);
//        }
//
//        $this->form->addElement('category');
//
//        $checkboxes = array();
//        $checkboxes[] = '<script type="text/javascript">';
//        $checkboxes[] = '$(document).ready(function() {';
//        $checkboxes[] = '$("input:checkbox[name=\'' . Invitation :: PROPERTY_ANONYMOUS . '\']").iphoneStyle({ checkedLabel: \'' . Translation :: get('Yes') . '\', uncheckedLabel: \'' . Translation :: get('No') . '\'});';
//        $checkboxes[] = '});';
//        $checkboxes[] = '</script>';
//
//        $this->form->addElement('html', implode("\n", $checkboxes));
//        $this->form->setDefaults(array('forever' => 1));
//    }

    function process()
    {
        $invitation_parameters = $this->form->get_invitation_parameters();
        $emails = $invitation_parameters->get_emails();
        $properties = $invitation_parameters->get_properties();
        $existing_users = array();

        foreach ($emails as $email)
        {
            $email_condition = new EqualityCondition(User :: PROPERTY_EMAIL, $email);
            $users = UserDataManager :: get_instance()->retrieve_users($email_condition);

            if ($users->size() > 0)
            {
                while ($user = $users->next_result())
                {
                    $existing_users[] = $user->get_id();
                }
            }
            else
            {
                $invitation_conditions = array();
                $invitation_conditions[] = new EqualityCondition(Invitation :: PROPERTY_EMAIL, $email);
                $invitation_conditions[] = new EqualityCondition(Invitation :: PROPERTY_PARAMETERS, $properties[Invitation :: PROPERTY_PARAMETERS]);
                $invitation_condition = new AndCondition($invitation_conditions);

                $count = AdminDataManager :: get_instance()->count_invitations($invitation_condition);

                if ($count > 0)
                {
                    $invitations = AdminDataManager :: get_instance()->retrieve_invitations($invitation_condition);

                    while ($invitation = $invitations->next_result())
                    {
                        $invitation->set_expiration_date($properties[Invitation :: PROPERTY_EXPIRATION_DATE]);
                        $invitation->set_anonymous($properties[Invitation :: PROPERTY_ANONYMOUS]);
                        $invitation->set_title($properties[Invitation :: PROPERTY_TITLE]);
                        $invitation->set_message($properties[Invitation :: PROPERTY_MESSAGE]);
                        $invitation->set_rights_templates($properties[Invitation :: PROPERTY_RIGHTS_TEMPLATES]['template']);
                        $invitation->update();
                    }
                }
                else
                {
                    $invitation = new Invitation($properties);
                    $invitation->set_email($email);
                    $invitation->create();
                }
            }
        }

        $success = $this->form->process_existing_users($existing_users);

        return $success;
    }
}
?>