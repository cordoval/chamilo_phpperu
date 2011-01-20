<?php
namespace common\extensions\external_repository_manager\implementation\dropbox;

use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\StringUtilities;
use common\libraries\FormValidator;
use common\libraries\Request;

use common\extensions\external_repository_manager\ExternalRepositoryObjectDisplay;
/**
 * $Id: dropbox_external_repository_manager_form.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package
 */

class DropboxExternalRepositoryManagerForm extends FormValidator
{

    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const TYPE_NEWFOLDER = 3;

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
    	elseif ($this->form_type == self :: TYPE_NEWFOLDER)
        {
            $this->build_newfolder_form();
        }

        $this->setDefaults();
    }

    public function set_external_repository_object(DropboxExternalRepositoryObject $external_repository_object)
    {
        $this->external_repository_object = $external_repository_object;

        $defaults[DropboxExternalRepositoryObject :: PROPERTY_ID] = $external_repository_object->get_id();        

        $display = ExternalRepositoryObjectDisplay :: factory($external_repository_object);
        $defaults[self :: PREVIEW] = $display->get_preview();

        parent :: setDefaults($defaults);
    }
    
    function build_basic_form()
    {
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->add_information_message('dropbox_api_move', null, Translation :: get('DropboxAPIMoveImpossible'));
        }        
    }

    function build_editing_form()
    {
        $this->addElement('static', self :: PREVIEW);

        $this->build_basic_form();

        $this->addElement('hidden', DropboxExternalRepositoryObject :: PROPERTY_ID);

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_file()
    {
        return $this->application->get_external_repository_manager_connector()->update_external_repository_object($this->exportValues());
    }

    function upload_file()
    {
        if (StringUtilities :: has_value(($_FILES[self :: FILE]['name'])))
        {
	        if($this->application->get_external_repository_manager_connector()->create_external_repository_object($_FILES[self :: FILE]['name'], $_FILES[self :: FILE]['tmp_name']))
            	return $_FILES[self :: FILE]['name'];
        }
        else
        {
            return null;
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
    
	function build_newfolder_form()
    {
    	$this->addElement('text', 'foldername', 'Name of new folder', array('size' => '50'));
        $this->addRule('foldername', Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
    	
        $this->addElement('hidden', 'folder');
        $this->setDefaults(array('folder' => Request :: get('folder')));
        
    	$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);    	
    }
    
	function create_folder($folder)
    {       	
    	if(!is_null($_POST['foldername'] && !is_null($_POST['folder'])))
    	{
    		return $this->application->get_external_repository_manager_connector()->create_external_repository_folder($_POST['folder'] . '/'. $_POST['foldername']);   		
    	}
    	else return null;
    }
}
?>