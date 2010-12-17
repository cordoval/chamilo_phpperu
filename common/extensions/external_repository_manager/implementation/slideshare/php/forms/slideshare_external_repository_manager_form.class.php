<?php
namespace common\extensions\external_repository_manager\implementation\slideshare;

use common\libraries\FormValidator;
use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\StringUtilities;
use common\extensions\external_repository_manager\ExternalRepositoryObjectDisplay;
/**
 * $Id: slideshare_external_repository_manager_form.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package
 */

class SlideshareExternalRepositoryManagerForm extends FormValidator
{

    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;

    const PREVIEW = 'preview';
    const FILE = 'file';

    private $application;
    private $form_type;
    private $external_repository_object;

    function __construct($form_type, $action, $application)
    {
        parent :: __construct(Utilities :: get_classname_from_object($this, true), 'post', $action);

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

    public function set_external_repository_object(SlideshareExternalRepositoryObject $external_repository_object)
    {
        $this->external_repository_object = $external_repository_object;

        $defaults[SlideshareExternalRepositoryObject :: PROPERTY_ID] = $external_repository_object->get_id();
        $defaults[SlideshareExternalRepositoryObject :: PROPERTY_TITLE] = $external_repository_object->get_title();                
		$defaults[SlideshareExternalRepositoryObject :: PROPERTY_DESCRIPTION] = $external_repository_object->get_description();
        
        $display = ExternalRepositoryObjectDisplay :: factory($external_repository_object);
        $defaults[self :: PREVIEW] = $display->get_preview();

        parent :: setDefaults($defaults);
    }    

    function build_basic_form()
    {
        $this->addElement('text', SlideshareExternalRepositoryObject :: PROPERTY_TITLE, Translation :: get('Title', null, Utilities :: COMMON_LIBRARIES), array('size' => '50'));
        $this->addRule(SlideshareExternalRepositoryObject :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');

        $this->addElement('textarea', SlideshareExternalRepositoryObject :: PROPERTY_DESCRIPTION, Translation :: get('Description', null, Utilities :: COMMON_LIBRARIES), array("rows" => "7", "cols" => "80"));
    }

    function build_editing_form()
    {
        $this->addElement('static', self :: PREVIEW);

        $this->build_basic_form();

        $this->addElement('hidden', SlideshareExternalRepositoryObject :: PROPERTY_ID);

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_slideshow()
    {
        return $this->application->get_external_repository_manager_connector()->update_external_repository_object($this->exportValues());
    }

    function upload_slideshow()
    {
        if (StringUtilities :: has_value(($_FILES[self :: FILE]['name'])))
        {
            return $this->application->get_external_repository_manager_connector()->create_external_repository_object($this->exportValues(), $_FILES[self :: FILE]);
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

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }    	
}
?>