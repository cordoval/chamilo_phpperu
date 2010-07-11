<?php
/**
 * Description of mediamosa_external_repository_settings_formclass
 *
 * @author jevdheyd
 */
class MediamosaExternalRepositoryManagerSettingsForm extends FormValidator {

    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;

    private $form_type;
    private $server_object;
    private $component;
    
    function MediamosaExternalRepositoryManagerSettingsForm($form_type, $action, $component)
    {
        parent :: __construct('mediamosa_setting', 'post', $action);

        $this->form_type = $form_type;
        $this->component = $component;

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
        $versions =  array();
        $versions['1.7.4'] = '1.7.4';

        $this->addElement('text', ExternalRepositoryServerObject :: PROPERTY_TITLE, Translation :: get(ExternalRepositoryServerObject :: PROPERTY_TITLE));
        $this->addRule(ExternalRepositoryServerObject::PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement('text', ExternalRepositoryServerObject :: PROPERTY_URL, Translation :: get(ExternalRepositoryServerObject :: PROPERTY_URL));
        $this->addRule(ExternalRepositoryServerObject::PROPERTY_URL, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement('text', ExternalRepositoryServerObject :: PROPERTY_LOGIN, Translation :: get(ExternalRepositoryServerObject :: PROPERTY_LOGIN));
        $this->addRule(ExternalRepositoryServerObject::PROPERTY_LOGIN, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement('text', ExternalRepositoryServerObject :: PROPERTY_PASSWORD, Translation :: get(ExternalRepositoryServerObject :: PROPERTY_PASSWORD));
        $this->addRule(ExternalRepositoryServerObject::PROPERTY_PASSWORD, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement('select', ExternalRepositoryServerObject :: PROPERTY_VERSION, Translation :: get(ExternalRepositoryServerObject :: PROPERTY_VERSION), $versions);
        $this->addElement('text', ExternalRepositoryServerObject :: PROPERTY_DEFAULT_USER_QUOTUM, Translation :: get(ExternalRepositoryServerObject :: PROPERTY_DEFAULT_USER_QUOTUM));
        $this->addRule(ExternalRepositoryServerObject::PROPERTY_DEFAULT_USER_QUOTUM, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement('checkbox', ExternalRepositoryServerObject :: PROPERTY_IS_UPLOAD_POSSIBLE, Translation :: get(ExternalRepositoryServerObject :: PROPERTY_IS_UPLOAD_POSSIBLE));
        $this->addElement('checkbox', ExternalRepositoryServerObject :: PROPERTY_IS_DEFAULT, Translation :: get(ExternalRepositoryServerObject :: PROPERTY_IS_DEFAULT));
    }

    function build_creation_form()
    {
        $this->build_basic_form();
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_editing_form()
    {
        $this->build_basic_form();

        $this->addElement('hidden', ExternalRepositoryServerObject :: PROPERTY_ID);

        $buttons[] = $this->createElement('style_submit_button', 'edit', Translation :: get('Edit'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function set_server_object($object)
    {
        $defaults = array();

        $defaults[ExternalRepositoryServerObject :: PROPERTY_ID] = $object->get_id();
        $defaults[ExternalRepositoryServerObject :: PROPERTY_TITLE] = $object->get_title();
        $defaults[ExternalRepositoryServerObject :: PROPERTY_URL] = $object->get_url();
        $defaults[ExternalRepositoryServerObject :: PROPERTY_LOGIN] = $object->get_login();
        $defaults[ExternalRepositoryServerObject :: PROPERTY_PASSWORD] = $object->get_password();
        $defaults[ExternalRepositoryServerObject :: PROPERTY_VERSION] = $object->get_version();
        $defaults[ExternalRepositoryServerObject :: PROPERTY_DEFAULT_USER_QUOTUM] = $object->get_default_user_quotum();
        $defaults[ExternalRepositoryServerObject :: PROPERTY_IS_DEFAULT] = $object->get_is_default();
        $defaults[ExternalRepositoryServerObject :: PROPERTY_IS_UPLOAD_POSSIBLE] = $object->get_is_upload_possible();
        
        $this->setDefaults($defaults);
    }

    function create_setting()
    {
        $object = new ExternalRepositoryServerObject();

        $object->set_title($this->exportValue(ExternalRepositoryServerObject :: PROPERTY_TITLE));
        $object->set_url($this->exportValue(ExternalRepositoryServerObject :: PROPERTY_URL));
        $object->set_login($this->exportValue(ExternalRepositoryServerObject :: PROPERTY_LOGIN));
        $object->set_password($this->exportValue(ExternalRepositoryServerObject :: PROPERTY_PASSWORD));
        $object->set_version($this->exportValue(ExternalRepositoryServerObject :: PROPERTY_VERSION));
        $object->set_default_user_quotum($this->exportValue(ExternalRepositoryServerObject :: PROPERTY_DEFAULT_USER_QUOTUM));
        if($this->exportValue(ExternalRepositoryServerObject :: PROPERTY_IS_UPLOAD_POSSIBLE) == 1)
        {
            $object->set_is_upload_possible(1);
        }
        else
        {
            $object->set_is_upload_possible(0);
        }

        if($this->exportValue(ExternalRepositoryServerObject :: PROPERTY_IS_DEFAULT) == 1)
        {
            $object->set_is_default(1);
        }
        else
        {
            $object->set_is_default(0);
        }

        if($object->create())
        {
            return true;
        }
        
        return false;
    }

    function update_setting()
    {
        $object = new ExternalRepositoryServerObject();

        $object->set_id($this->exportValue(ExternalRepositoryServerObject :: PROPERTY_ID));
        $object->set_title($this->exportValue(ExternalRepositoryServerObject :: PROPERTY_TITLE));
        $object->set_url($this->exportValue(ExternalRepositoryServerObject :: PROPERTY_URL));
        $object->set_login($this->exportValue(ExternalRepositoryServerObject :: PROPERTY_LOGIN));
        $object->set_password($this->exportValue(ExternalRepositoryServerObject :: PROPERTY_PASSWORD));
        $object->set_version($this->exportValue(ExternalRepositoryServerObject :: PROPERTY_VERSION));
        $object->set_default_user_quotum($this->exportValue(ExternalRepositoryServerObject :: PROPERTY_DEFAULT_USER_QUOTUM));
        if($this->exportValue(ExternalRepositoryServerObject :: PROPERTY_IS_UPLOAD_POSSIBLE) == 1)
        {
            $object->set_is_upload_possible(1);
        }
        else
        {
            $object->set_is_upload_possible(0);
        }

        if($this->exportValue(ExternalRepositoryServerObject :: PROPERTY_IS_DEFAULT) == 1)
        {
            $object->set_is_default(1);
        }
        else
        {
            $object->set_is_default(0);
        }

        if($object->update()) return true;

        return false;
    }

    function setDefaults($defaults = array())
    {
       parent :: setDefaults($defaults);
    }
}
?>
