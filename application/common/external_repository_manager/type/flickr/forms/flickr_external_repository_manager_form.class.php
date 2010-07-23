<?php
/**
 * $Id: flickr_external_repository_manager_form.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package
 */

class FlickrExternalRepositoryManagerForm extends FormValidator
{

    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;

    const PREVIEW = 'preview';
    const FILE = 'file';

    private $application;
    private $form_type;
    private $external_repository_object;

    function FlickrExternalRepositoryManagerForm($form_type, $action, $application)
    {
        parent :: __construct(Utilities :: camelcase_to_underscores(get_class($this)), 'post', $action);

        $this->application = $application;

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

    public function set_external_repository_object(FlickrExternalRepositoryObject $external_repository_object)
    {
        $this->external_repository_object = $external_repository_object;

        $defaults[FlickrExternalRepositoryObject :: PROPERTY_ID] = $external_repository_object->get_id();
        $defaults[FlickrExternalRepositoryObject :: PROPERTY_TITLE] = $external_repository_object->get_title();
        $defaults[FlickrExternalRepositoryObject :: PROPERTY_DESCRIPTION] = html_entity_decode($external_repository_object->get_description());
        $defaults[FlickrExternalRepositoryObject :: PROPERTY_TAGS] = $external_repository_object->get_tags_string(false);

        $display = ExternalRepositoryObjectDisplay :: factory($external_repository_object);
        $defaults[self :: PREVIEW] = $display->get_preview();

        parent :: setDefaults($defaults);
    }

    public function get_tags()
    {
        $external_repository_object = $this->external_repository_object;
        return implode(",", $external_repository_object->get_tags());
    }

    function build_basic_form()
    {
        $this->addElement('text', FlickrExternalRepositoryObject :: PROPERTY_TITLE, Translation :: get('Title'), array('size' => '50'));
        $this->addRule(FlickrExternalRepositoryObject :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');

        $this->addElement('textarea', FlickrExternalRepositoryObject :: PROPERTY_TAGS, Translation :: get('Tags'), array('rows' => '2', 'cols' => '80'));

        $this->addElement('textarea', FlickrExternalRepositoryObject :: PROPERTY_DESCRIPTION, Translation :: get('Description'), array('rows' => '7', 'cols' => '80'));
    }

    function build_editing_form()
    {
        $this->addElement('static', self :: PREVIEW);

        $this->build_basic_form();

        $this->addElement('hidden', FlickrExternalRepositoryObject :: PROPERTY_ID);

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Edit'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_photo()
    {
        return FlickrExternalRepositoryConnector :: get_instance($this->application->get_external_repository())->update_external_repository_object($this->exportValues());
    }

    function upload_photo()
    {
        if (StringUtilities :: has_value(($_FILES[self :: FILE]['name'])))
        {
            return FlickrExternalRepositoryConnector :: get_instance($this->application->get_external_repository())->create_external_repository_object($this->exportValues(), $_FILES[self :: FILE]['tmp_name']);
        }
        else
        {
            return false;
        }
    }

    function build_creation_form()
    {
        $this->build_basic_form();

        $this->addElement('file', self :: FILE, Translation :: get('FileName'));

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
}
?>