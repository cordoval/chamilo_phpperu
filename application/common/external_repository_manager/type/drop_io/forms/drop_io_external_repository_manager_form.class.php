<?php
/**
 * 
 * $Id: drop_io_external_repository_manager_form.class.php 
 * @package
 */

class DropIoExternalRepositoryManagerForm extends FormValidator
{

    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
//    const RESULT_SUCCESS = 'GroupUpdated';
//    const RESULT_ERROR = 'GroupUpdateFailed';

    const VIDEO_TITLE = 'title';
    const VIDEO_DESCRIPTION = 'description';

    private $application;
//    private $video_entry;
    private $form_type;
    private $external_repository_object;

    function DropIoExternalRepositoryManagerForm($form_type, $action, $application)
    {
        parent :: __construct('drop_io_upload', 'post', $action);

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

    public function set_external_repository_object($object)
    {
        $this->external_repository_object = $object;
    
        $this->addElement('hidden', MatterhornExternalRepositoryObject :: PROPERTY_ID);
        $defaults[DropIoExternalRepositoryObject :: PROPERTY_TITLE] = $object->get_title();
        $defaults[DropIoExternalRepositoryObject :: PROPERTY_DESCRIPTION] = $object->get_description();
        $defaults[DropIoExternalRepositoryObject :: PROPERTY_VERSION] = $object->get_version();
        $defaults[DropIoExternalRepositoryObject :: PROPERTY_FORMAT] = $object->get_format();
		$defaults[DropIoExternalRepositoryObject :: PROPERTY_EXPIRATION_LENGTH] = $object->get_expiration_length();

        parent :: setDefaults($defaults);
    }

    function build_basic_form()
    {
        $this->addElement('text', DropIoExternalRepositoryObject :: PROPERTY_TITLE, Translation :: get('Title'), array("size" => "50"));
        $this->addRule(DropIoExternalRepositoryObject :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement('textarea', DropIoExternalRepositoryObject :: PROPERTY_DESCRIPTION, Translation :: get('Description'), array("rows" => "7", "cols" => "80"));
		$this->addElement('text', DropIoExternalRepositoryObject :: PROPERTY_VERSION, Translation :: get('Version'), array("size" => "10"));
		$this->addElement('text', DropIoExternalRepositoryObject :: PROPERTY_FORMAT, Translation :: get('Format'), array("size" => "50"));
       	$this->addElement('text', DropIoExternalRepositoryObject :: PROPERTY_EXPIRATION_LENGTH, Translation :: get('ExpirationLength'), array("size" => "50"));
    }

    function build_editing_form()
    {
        $this->build_basic_form();

        $this->addElement('hidden', DropIoExternalRepositoryObject :: PROPERTY_ID);

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Edit'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
        $this->build_basic_form();
		$this->addElement('file', 'track', Translation :: get('File'));
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
}
?>