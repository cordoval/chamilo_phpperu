<?php
/**
 * @package admin.lib
 * $Id: system_announcement_publication_form.class.php 170 2009-11-12 12:21:00Z vanpouckesven $
 */
require_once Path :: get_plugin_path() . 'html2text/class.html2text.inc';
/**
 * This class represents a form to allow a user to publish a learning object.
 *
 * The form allows the user to set some properties of the publication
 * (publication dates, target users, visibility, ...)
 */
class SystemAnnouncementPublicationForm extends FormValidator
{
    /**#@+
     * Constant defining a form parameter
     */
    
    const TYPE_SINGLE = 1;
    const TYPE_MULTI = 2;
    
    const PARAM_FOREVER = 'forever';
    const PARAM_FROM_DATE = 'from_date';
    const PARAM_TO_DATE = 'to_date';
    const PARAM_TARGETS = 'target_users_and_groups';
    const PARAM_RECEIVERS = 'receivers';
    const PARAM_TARGETS_TO = 'to';
    const PARAM_TARGET_USER_PREFIX = 'user';
    const PARAM_TARGET_GROUP_PREFIX = 'group';
    
    const PARAM_TARGET = 'target_users_and_groups';
    const PARAM_TARGET_ELEMENTS = 'target_users_and_groups_elements';
    const PARAM_TARGET_OPTION = 'target_users_and_groups_option';
    
    /**#@-*/
    /**
     * The learning object that will be published
     */
    private $content_object;
    /**
     * The publication that will be changed (when using this form to edit a
     * publication)
     */
    private $form_user;
    
    private $form_type;
    
    private $system_announcement_publication;

    /**
     * Creates a new learning object publication form.
     * @param ContentObject The learning object that will be published
     * @param string $tool The tool in which the object will be published
     * @param boolean $email_option Add option in form to send the learning
     * object by email to the receivers
     */
    function SystemAnnouncementPublicationForm($form_type, $content_object, $form_user, $action)
    {
        parent :: __construct('publish', 'post', $action);
        $this->form_type = $form_type;
        $this->content_object = $content_object;
        $this->form_user = $form_user;
        
        switch ($this->form_type)
        {
            case self :: TYPE_SINGLE :
                $this->build_single_form();
                break;
            case self :: TYPE_MULTI :
                $this->build_multi_form();
                break;
        }
        $this->add_footer();
        $this->setDefaults();
    }

    /**
     * Sets the default values of the form.
     *
     * By default the publication is for everybody who has access to the tool
     * and the publication will be available forever.
     */
    function setDefaults()
    {
        $defaults = array();
        $defaults[self :: PARAM_TARGET_OPTION] = 0;
        $defaults[self :: PARAM_FOREVER] = 1;
        parent :: setDefaults($defaults);
    }

    function build_single_form()
    {
        $this->build_form();
    }

    function build_multi_form()
    {
        $this->build_form();
        $this->addElement('hidden', 'ids', serialize($this->content_object));
    }

    function add_footer()
    {
        $buttons = array();
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Publish'), array('class' => 'positive publish'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Builds the form by adding the necessary form elements.
     */
    function build_form()
    {
        $attributes = array();
        $attributes['search_url'] = Path :: get(WEB_PATH) . 'common/xml_feeds/xml_user_group_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('PublishFor');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $attributes['locale'] = $locale;
        $attributes['exclude'] = array('user_' . $this->form_user->get_id());
        $attributes['defaults'] = array();
        $this->add_receivers(self :: PARAM_TARGET, Translation :: get('PublishFor'), $attributes);
        
        $this->add_forever_or_timewindow();
        $this->addElement('checkbox', SystemAnnouncementPublication :: PROPERTY_HIDDEN, Translation :: get('Hidden'));
    }

    /**
     * Creates a learning object publication using the values from the form.
     * @return ContentObjectPublication The new publication
     */
    function create_content_object_publication()
    {
        $values = $this->exportValues();
        
        if ($values[self :: PARAM_FOREVER] != 0)
        {
            $from = $to = 0;
        }
        else
        {
            $from = Utilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]);
            $to = Utilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]);
        }
        $hidden = ($values[SystemAnnouncementPublication :: PROPERTY_HIDDEN] ? 1 : 0);
        
        $users = $values[self :: PARAM_TARGET_ELEMENTS]['user'];
        $groups = $values[self :: PARAM_TARGET_ELEMENTS]['group'];
        
        $pub = new SystemAnnouncementPublication();
        $pub->set_content_object_id($this->content_object->get_id());
        $pub->set_publisher($this->form_user->get_id());
        $pub->set_published(time());
        $pub->set_modified(time());
        $pub->set_hidden($hidden);
        $pub->set_from_date($from);
        $pub->set_to_date($to);
        $pub->set_target_groups($groups);
        $pub->set_target_users($users);
        
        if ($pub->create())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function set_system_announcement_publication($system_announcement_publication)
    {
        $this->system_announcement_publication = $system_announcement_publication;
        
        $this->addElement('hidden', 'said');
        $this->addElement('hidden', 'action');
        
        $defaults['action'] = 'edit';
        $defaults['said'] = $system_announcement_publication->get_id();
        $defaults[SystemAnnouncementPublication :: PROPERTY_FROM_DATE] = $system_announcement_publication->get_from_date();
        $defaults[SystemAnnouncementPublication :: PROPERTY_TO_DATE] = $system_announcement_publication->get_to_date();
        if ($defaults[SystemAnnouncementPublication :: PROPERTY_FROM_DATE] != 0)
        {
            $defaults[self :: PARAM_FOREVER] = 0;
        }
        $defaults[SystemAnnouncementPublication :: PROPERTY_HIDDEN] = $system_announcement_publication->is_hidden();
        
        $udm = UserDataManager :: get_instance();
        $gdm = GroupDataManager :: get_instance();
        
        $target_groups = $this->system_announcement_publication->get_target_groups();
        $target_users = $this->system_announcement_publication->get_target_users();
        
        $defaults[self :: PARAM_TARGET_ELEMENTS] = array();
        foreach ($target_groups as $target_group)
        {
            $group = $gdm->retrieve_group($target_group);
            
            $selected_group = array();
            $selected_group['id'] = 'group_' . $group->get_id();
            $selected_group['classes'] = 'type type_group';
            $selected_group['title'] = $group->get_name();
            $selected_group['description'] = $group->get_description();
            
            $defaults[self :: PARAM_TARGET_ELEMENTS][$selected_group['id']] = $selected_group;
        }
        foreach ($target_users as $target_user)
        {
            $user = $udm->retrieve_user($target_user);
            
            $selected_user = array();
            $selected_user['id'] = 'user_' . $user->get_id();
            $selected_user['classes'] = 'type type_user';
            $selected_user['title'] = $user->get_fullname();
            $selected_user['description'] = $user->get_username();
            
            $defaults[self :: PARAM_TARGET_ELEMENTS][$selected_user['id']] = $selected_user;
        }
        
        if (count($defaults[self :: PARAM_TARGET_ELEMENTS]) > 0)
        {
            $defaults[self :: PARAM_TARGET_OPTION] = '1';
        }
        
        $active = $this->getElement(self :: PARAM_TARGET_ELEMENTS);
        $active->_elements[0]->setValue(serialize($defaults[self :: PARAM_TARGET_ELEMENTS]));
        
        parent :: setDefaults($defaults);
    }

    function update_content_object_publication()
    {
        $values = $this->exportValues();
        
        if ($values[self :: PARAM_FOREVER] != 0)
        {
            $from = $to = 0;
        }
        else
        {
            $from = Utilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]);
            $to = Utilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]);
        }
        $hidden = ($values[SystemAnnouncementPublication :: PROPERTY_HIDDEN] ? 1 : 0);
        
        $users = $values[self :: PARAM_TARGET_ELEMENTS]['user'];
        $groups = $values[self :: PARAM_TARGET_ELEMENTS]['group'];
        
        $pub = $this->system_announcement_publication;
        $pub->set_modified(time());
        $pub->set_hidden($hidden);
        $pub->set_from_date($from);
        $pub->set_to_date($to);
        $pub->set_target_groups($groups);
        $pub->set_target_users($users);
        
        if ($pub->update())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function create_content_object_publications()
    {
        $values = $this->exportValues();

        $ids = unserialize($values['ids']);
        
        foreach ($ids as $id)
        {
            if ($values[self :: PARAM_FOREVER] != 0)
            {
                $from = $to = 0;
            }
            else
            {
                $from = Utilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]);
                $to = Utilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]);
            }
            $hidden = ($values[SystemAnnouncementPublication :: PROPERTY_HIDDEN] ? 1 : 0);
            
            $users = $values[self :: PARAM_TARGET_ELEMENTS]['user'];
            $groups = $values[self :: PARAM_TARGET_ELEMENTS]['group'];

            $pub = new SystemAnnouncementPublication();
            $pub->set_content_object_id($id);
            $pub->set_publisher($this->form_user->get_id());
            $pub->set_published(time());
            $pub->set_modified(time());
            $pub->set_hidden($hidden);
            $pub->set_from_date($from);
            $pub->set_to_date($to);
            $pub->set_target_groups($groups);
            $pub->set_target_users($users);
            
            if (! $pub->create())
            {
                return false;
            }
        }
        return true;
    }
}
?>