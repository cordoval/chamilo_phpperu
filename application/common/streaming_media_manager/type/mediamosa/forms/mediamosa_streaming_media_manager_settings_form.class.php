<?php
/**
 * Description of mediamosa_streaming_media_settings_formclass
 *
 * @author jevdheyd
 */
class MediamosaStreamingMediaManagerSettingsForm extends FormValidator {

    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;

    private $form_type;
    private $server_object;
    
    function MediamosaStreamingMediaManagerSettingsForm($form_type, $action)
    {
        parent :: __construct('mediamosa_setting', 'post', $action);

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
        $this->addElement('text', StreamingMediaServerObject :: PROPERTY_TITLE, Translation :: get(StreamingMediaServerObject :: PROPERTY_TITLE));
        $this->addRule(StreamingMediaServerObject::PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement('text', StreamingMediaServerObject :: PROPERTY_URL, Translation :: get(StreamingMediaServerObject :: PROPERTY_URL));
        $this->addRule(StreamingMediaServerObject::PROPERTY_URL, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement('text', StreamingMediaServerObject :: PROPERTY_LOGIN, Translation :: get(StreamingMediaServerObject :: PROPERTY_LOGIN));
        $this->addRule(StreamingMediaServerObject::PROPERTY_LOGIN, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement('text', StreamingMediaServerObject :: PROPERTY_PASSWORD, Translation :: get(StreamingMediaServerObject :: PROPERTY_PASSWORD));
        $this->addRule(StreamingMediaServerObject::PROPERTY_PASSWORD, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement('checkbox', StreamingMediaServerObject :: PROPERTY_IS_UPLOAD_POSSIBLE, Translation :: get(StreamingMediaServerObject :: PROPERTY_IS_UPLOAD_POSSIBLE));
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

        $this->addElement('hidden', StreamingMediaServerObject :: PROPERTY_ID);

        $buttons[] = $this->createElement('style_submit_button', 'edit', Translation :: get('Edit'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function set_server_object($object)
    {
        $defaults = array();

        $defaults[StreamingMediaServerObject :: PROPERTY_ID] = $object->get_id();
        $defaults[StreamingMediaServerObject :: PROPERTY_TITLE] = $object->get_title();
        $defaults[StreamingMediaServerObject :: PROPERTY_URL] = $object->get_url();
        $defaults[StreamingMediaServerObject :: PROPERTY_LOGIN] = $object->get_login();
        $defaults[StreamingMediaServerObject :: PROPERTY_PASSWORD] = $object->get_password();
        $defaults[StreamingMediaServerObject :: PROPERTY_IS_UPLOAD_POSSIBLE] = $object->get_is_upload_possible();
        
        $this->setDefaults($defaults);
    }

    function create_setting()
    {
        $object = new StreamingMediaServerObject();

        $object->set_title($this->exportValue(StreamingMediaServerObject :: PROPERTY_TITLE));
        $object->set_url($this->exportValue(StreamingMediaServerObject :: PROPERTY_URL));
        $object->set_login($this->exportValue(StreamingMediaServerObject :: PROPERTY_LOGIN));
        $object->set_password($this->exportValue(StreamingMediaServerObject :: PROPERTY_PASSWORD));
        if($this->exportValue(StreamingMediaServerObject :: PROPERTY_IS_UPLOAD_POSSIBLE) == 1)
        {
            $object->set_is_upload_possible(1);
        }
        else
        {
            $object->set_is_upload_possible(0);
        }

        if($object->create()) return true;
        
        return false;
    }

    function update_setting()
    {
        $object = new StreamingMediaServerObject();

        $object->set_id($this->exportValue(StreamingMediaServerObject :: PROPERTY_ID));
        $object->set_title($this->exportValue(StreamingMediaServerObject :: PROPERTY_TITLE));
        $object->set_url($this->exportValue(StreamingMediaServerObject :: PROPERTY_URL));
        $object->set_login($this->exportValue(StreamingMediaServerObject :: PROPERTY_LOGIN));
        $object->set_password($this->exportValue(StreamingMediaServerObject :: PROPERTY_PASSWORD));
        if($this->exportValue(StreamingMediaServerObject :: PROPERTY_IS_UPLOAD_POSSIBLE) == 1)
        {
            $object->set_is_upload_possible(1);
        }
        else
        {
            $object->set_is_upload_possible(0);
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
