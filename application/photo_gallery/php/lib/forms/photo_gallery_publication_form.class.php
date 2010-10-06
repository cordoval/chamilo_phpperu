<?php
/**
 * $Id: photo_gallery_publication_form.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.photo_gallery.forms
 */
require_once WebApplication :: get_application_class_lib_path('photo_gallery') . 'photo_gallery_publication.class.php';

class PhotoGalleryPublicationForm extends FormValidator
{
     /**#@+
     * Constant defining a form parameter
     */

    const TYPE_SINGLE = 1;
    const TYPE_MULTI = 2;

    const PARAM_SHARE = 'share_users_and_groups';
    const PARAM_SHARE_ELEMENTS = 'share_users_and_groups_elements';
    const PARAM_SHARE_OPTION = 'share_users_and_groups_option';

    /**#@-*/
    /**
     * The learning object that will be published
     */
    private $content_object;
    /**
     * The publication that will be changed (when using this form to edit a
     * publication)
     */
    private $publication;
    /**
     * The publication that will be changed (when using this form to edit a
     * publication)
     */
    private $form_user;

    private $form_type;

    /**
     * Creates a new learning object publication form.
     * @param ContentObject The learning object that will be published
     * @param string $tool The tool in which the object will be published
     * @param boolean $email_option Add option in form to send the learning
     * object by email to the receivers
     */
    function PhotoGalleryPublicationForm($form_type, $content_object, $form_user, $action)
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
        $defaults[self :: PARAM_SHARE_OPTION] = 0;
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

    /**
     * Builds the form by adding the necessary form elements.
     */
    function build_form()
    {
        $shares = array();
        $attributes = array();
        $attributes['search_url'] = Path :: get(WEB_PATH) . 'common/xml_feeds/xml_user_group_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('ShareWith');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $attributes['locale'] = $locale;
        $attributes['exclude'] = array('user_' . $this->form_user->get_id());
        $attributes['defaults'] = array();

        $this->add_receivers(self :: PARAM_SHARE, Translation :: get('ShareWith'), $attributes, 'Nobody');
    }

    function add_footer()
    {
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Publish'), array('class' => 'positive publish'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        //$this->addElement('submit', 'submit', Translation :: get('Ok'));
    }

    /**
     * Creates a learning object publication using the values from the form.
     * @return ContentObjectPublication The new publication
     */
    function create_content_object_publication()
    {
        $values = $this->exportValues();

        $users = $values[self :: PARAM_SHARE_ELEMENTS]['user'];
        $groups = $values[self :: PARAM_SHARE_ELEMENTS]['group'];

        $pub = new PersonalCalendarPublication();
        $pub->set_content_object_id($this->content_object->get_id());
        $pub->set_publisher($this->form_user->get_id());
        $pub->set_published(time());
        $pub->set_target_users($users);
        $pub->set_target_groups($groups);

        if ($pub->create())
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

        $users = $values[self :: PARAM_SHARE_ELEMENTS]['user'];
        $groups = $values[self :: PARAM_SHARE_ELEMENTS]['group'];

        foreach ($ids as $id)
        {
            $pub = new PhotoGalleryPublication();
            $pub->set_content_object_id($id);
            $pub->set_publisher($this->form_user->get_id());
            $pub->set_published(time());
            $pub->set_target_users($users);
            $pub->set_target_groups($groups);

            if (! $pub->create())
            {
                return false;
            }
        }
        return true;
    }

    function set_publication($publication)
    {
        $this->publication = $publication;
        $this->addElement('hidden', 'pid');
        $this->addElement('hidden', 'action');
        $defaults['action'] = 'edit';
        $defaults['pid'] = $publication->get_id();

        $udm = UserDataManager :: get_instance();
        $gdm = GroupDataManager :: get_instance();

        $target_groups = $this->publication->get_target_groups();
        $target_users = $this->publication->get_target_users();

        $defaults[self :: PARAM_SHARE_ELEMENTS] = array();
        foreach ($target_groups as $target_group)
        {
            $group = $gdm->retrieve_group($target_group);

            $selected_group = array();
            $selected_group['id'] = 'group_' . $group->get_id();
            $selected_group['classes'] = 'type type_group';
            $selected_group['title'] = $group->get_name();
            $selected_group['description'] = $group->get_name();

            $defaults[self :: PARAM_SHARE_ELEMENTS][$selected_group['id']] = $selected_group;
        }
        foreach ($target_users as $target_user)
        {
            $user = $udm->retrieve_user($target_user);

            $selected_user = array();
            $selected_user['id'] = 'user_' . $user->get_id();
            $selected_user['classes'] = 'type type_user';
            $selected_user['title'] = $user->get_fullname();
            $selected_user['description'] = $user->get_username();

            $defaults[self :: PARAM_SHARE_ELEMENTS][$selected_user['id']] = $selected_user;
        }

        if (count($defaults[self :: PARAM_SHARE_ELEMENTS]) > 0)
        {
            $defaults[self :: PARAM_SHARE_OPTION] = '1';
        }

        $active = $this->getElement(self :: PARAM_SHARE_ELEMENTS);
        $active->_elements[0]->setValue(serialize($defaults[self :: PARAM_SHARE_ELEMENTS]));

        parent :: setDefaults($defaults);
    }

    function update_content_object_publication()
    {
        $values = $this->exportValues();

        $users = $values[self :: PARAM_SHARE_ELEMENTS]['user'];
        $groups = $values[self :: PARAM_SHARE_ELEMENTS]['group'];

        $pub = $this->publication;
        $pub->set_target_users($users);
        $pub->set_target_groups($groups);
        $pub->update();
        return $pub;
    }
}
?>