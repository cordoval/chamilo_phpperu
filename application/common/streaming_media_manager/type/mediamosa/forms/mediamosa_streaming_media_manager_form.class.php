<?php

/**
 * Description of mediamosa_streaming_media_manager_formclass
 *
 * @author jevdheyd
 */
class MediamosaStreamingMediaManagerForm extends FormValidator{

    private $application;
    private $form_type;
    private $streaming_media_object;

    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;

    const VIDEO_TITLE = 'title';
    const VIDEO_CATEGORY = 'category';
    //TODO: jens ->implement?? const VIDEO_TAGS = 'tags';
    const VIDEO_DESCRIPTION = 'description';

    function MediamosaStreamingMediaManagerForm($form_type, $atcion, $application)
    {
        parent :: __construct('mediamosa_upload', 'post', $action);

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

    function set_streaming_media_object($object)
    {
        $this->streaming_media_object = $object;
        
        $this->addElement(MediamosaStreamingMediaObject :: PROPERTY_ID);
        $defaults[MediamosaStreamingMediaObject :: PROPERTY_TITLE] = $object->get_title();
        $defaults[MediamosaStreamingMediaObject :: PROPERTY_DESCRIPTION] = $object->get_description();
        $defaults[MediamosaStreamingMediaObject :: PROPERTY_CREATOR] = $object->get_creator();
        //$defaults[MediamosaStreamingMediaObject :: PROPERTY_TAGS] = $object->get_tags();

        if(is_null($object->get_publisher))
        {
            $udm = UserDataManager :: get_instance();
            $user = $udm->retrieve_user(Session :: get_user_id());
            $defaults[MediamosaStreamingMediaObject :: PROPERTY_PUBLISHER] = $user->get_firstname().' '.$user->get_lastname();
        }
        else
        {
            $defaults[MediamosaStreamingMediaObject :: PROPERTY_PUBLISHER] = $object->get_publisher();
        }
        
        if(is_null($object->get_date()))
        {
            $defaults[MediamosaStreamingMediaObject :: PROPERTY_DATE_PUBLISHED] = date('Y-m-d H:i:s');
        }
        else
        {
            $defaults[MediamosaStreamingMediaObject :: PROPERTY_DATE_PUBLISHED] = $object->get_date();
        }

        $this->setDefaults($defaults);
    }

    function build_basic_form()
    {
        $this->addElement('text', MediamosaStreamingMediaObject::PROPERTY_TITLE, Translation :: get('Title'), array("size" => "50"));
        $this->addRule(MediaMosaStreamingMediaObject::PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');

        //$this->addElement('textarea', MediaMosaStreamingMediaObject::PROPERTY_TAGS, Translation :: get('Tags'), array("rows" => "1", "cols" => "80"));
        //$this->addRule(MediaMosaStreamingMediaObject::PROPERTY_TAGS, Translation :: get('ThisFieldIsRequired'), 'required');

        $this->addElement('textarea', MediaMosaStreamingMediaObject::PROPERTY_DESCRIPTION, Translation :: get('Description'), array("rows" => "7", "cols" => "110"));
        $this->addElement('text', MediamosaStreamingMediaObject :: PROPERTY_CREATOR, Translation :: get('Creator'), array("size" => "50"));

        $this->addElement('hidden', MediamosaStreamingMediaObject :: PROPERTY_PUBLISHER);
        $this->addelement('hidden', MediamosaStreamingMediaObject :: PROPERTY_DATE_PUBLISHED);

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

        $buttons[] = $this->createElement('style_submit_button', 'edit', Translation :: get('Create'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function setDefaults($defaults = array())
    {
        parent :: setDefaults($defaults);
    }

}
?>
