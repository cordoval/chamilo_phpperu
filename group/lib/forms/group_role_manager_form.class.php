<?php
/**
 * $Id: group_role_manager_form.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package groups.lib.forms
 */

class GroupRightsTemplateManagerForm extends FormValidator
{
    private $parent;
    private $group;
    private $form_group;

    /**
     * Creates a new UserForm
     * Used by the admin to create/update a group
     */
    function GroupRightsTemplateManagerForm($group, $form_group, $action)
    {
        parent :: __construct('group_rights_template_manager_form', 'post', $action);
        
        $this->group = $group;
        $this->form_group = $form_group;
        
        $this->build_basic_form();
    }

    /**
     * Creates a basic form
     */
    function build_basic_form()
    {
        // RightsTemplates element finder
        $group = $this->group;
        
        $linked_rights_templates = $group->get_rights_templates();
        $group_rights_templates = RightsUtilities :: rights_templates_for_element_finder($linked_rights_templates);
        
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
        
        $elem = $this->addElement('element_finder', 'rights_templates', null, $url, $locale, $group_rights_templates);
        $elem->setDefaults($defaults);
        
        // Submit button
        //$this->addElement('submit', 'group_settings', 'OK');
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
        $this->build_basic_form();
    }

    function update_group_rights_templates()
    {
        $group = $this->group;
        $values = $this->exportValues();
        return $group->update_rights_template_links($values['rights_templates']['template']);
    }

}
?>