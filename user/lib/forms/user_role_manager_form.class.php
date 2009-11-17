<?php
/**
 * $Id: user_role_manager_form.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.forms
 */

class UserRightsTemplateManagerForm extends FormValidator
{
    private $parent;
    private $user;
    private $form_user;

    /**
     * Creates a new UserForm
     * Used by the admin to create/update a user
     */
    function UserRightsTemplateManagerForm($user, $form_user, $action)
    {
        parent :: __construct('user_rights_template_manager_form', 'post', $action);
        
        $this->user = $user;
        $this->form_user = $form_user;
        
        $this->build_basic_form();
    }

    /**
     * Creates a basic form
     */
    function build_basic_form()
    {
        // RightsTemplates element finder
        $user = $this->user;
        
        $linked_rights_templates = $user->get_rights_templates();
        $user_rights_templates = RightsUtilities :: rights_templates_for_element_finder($linked_rights_templates);
        
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
        
        $elem = $this->addElement('element_finder', 'rights_templates', null, $url, $locale, $user_rights_templates);
        $elem->setDefaults($defaults);
        
        // Submit button
        //$this->addElement('submit', 'user_settings', 'OK');
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
        $this->build_basic_form();
    }

    function update_user_rights_templates()
    {
        $user = $this->user;
        $values = $this->exportValues();
        return $user->update_rights_template_links($values['rights_templates']);
    }

}
?>