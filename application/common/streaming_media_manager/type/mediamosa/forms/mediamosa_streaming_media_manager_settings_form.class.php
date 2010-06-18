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
    
    function MediamosaStreamingMediaSettingsForm($form_type, $action)
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
        $this->addElement('text', MediamosaStreamingMediaServerObject :: PROPERTY_TITLE, Translation :: get(MediamosaStreamingMediaServerObject :: PROPERTY_TITLE));
        $this->addRule(MediaMosaStreamingMediaServerObject::PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement('text', MediamosaStreamingMediaServerObject :: PROPERTY_LOGIN, Translation :: get(MediamosaStreamingMediaServerObject :: PROPERTY_LOGIN));
        $this->addRule(MediaMosaStreamingMediaServerObject::PROPERTY_LOGIN, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement('text', MediamosaStreamingMediaServerObject :: PROPERTY_PASSWORD, Translation :: get(MediamosaStreamingMediaServerObject :: PROPERTY_PASSWORD));
        $this->addRule(MediaMosaStreamingMediaServerObject::PROPERTY_PASSWORD, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement('checkbox', MediamosaStreamingMediaServerObject :: PROPERTY_IS_UPLOAD_POSSIBLE, Translation :: get(MediamosaStreamingMediaServerObject :: PROPERTY_IS_UPLOAD_POSSIBLE));
    }

    function build_creation_form()
    {
        $this->build_basic_form();
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_update_form()
    {
        $this->build_basic_form();

        $buttons[] = $this->createElement('style_submit_button', 'edit', Translation :: get('Create'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function set_server_object($object)
    {
        $defaults = array();

        $defaults[MediamosaStreamingMediaServerObject :: PROPERTY_ID] = $object->get_id();
        $defaults[MediamosaStreamingMediaServerObject :: PROPERTY_TITLE] = $object->get_title();
        $defaults[MediamosaStreamingMediaServerObject :: PROPERTY_LOGIN] = $object->get_login();
        $defaults[MediamosaStreamingMediaServerObject :: PROPERTY_PASSWORD] = $object->get_password();
        $defaults[MediamosaStreamingMediaServerObject :: PROPERTY_IS_UPLOAD_POSSIBLE] = $object->get_is_upload_possible();

        $this->setDefaults($defaults);
    }

    function create_setting()
    {
        
    }

    function update_setting()
    {

    }

}
?>
