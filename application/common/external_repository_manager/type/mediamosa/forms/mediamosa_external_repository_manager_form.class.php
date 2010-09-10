<?php

/**
 * Description of mediamosa_external_repository_manager_formclass
 *
 * @author jevdheyd
 */
class MediamosaExternalRepositoryManagerForm extends FormValidator{

    private $application;
    private $form_type;
    private $external_repository_object;

    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;

    const VIDEO_TITLE = 'title';
    const VIDEO_CATEGORY = 'category';
    const VIDEO_DESCRIPTION = 'description';

    function MediamosaExternalRepositoryManagerForm($form_type, $action, $application)
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

    function set_external_repository_object($object)
    {
       $this->external_repository_object = $object;

        $this->addElement(MediamosaExternalRepositoryObject :: PROPERTY_ID);
        $defaults[MediamosaExternalRepositoryObject :: PROPERTY_TITLE] = $object->get_title();
        $defaults[MediamosaExternalRepositoryObject :: PROPERTY_DESCRIPTION] = $object->get_description();
        $defaults[MediamosaExternalRepositoryObject :: PROPERTY_CREATOR] = $object->get_creator();
        //$defaults[MediamosaExternalRepositoryObject :: PROPERTY_TAGS] = $object->get_tags();
        $defaults[MediamosaExternalRepositoryObject :: PROPERTY_PUBLISHER] = $object->get_publisher();
        $defaults[MediamosaExternalRepositoryObject :: PROPERTY_DATE_PUBLISHED] = $object->get_date();
        $defaults[MediamosaExternalRepositoryObject :: PROPERTY_IS_DOWNLOADABLE] = $object->get_is_downloadable();
       
        $this->setDefaults($defaults);
    }

    function build_basic_form()
    {
        $this->addElement('text', MediamosaExternalRepositoryObject::PROPERTY_TITLE, Translation :: get('Title'), array("size" => "50"));
        $this->addRule(MediaMosaExternalRepositoryObject :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');

        //$this->addElement('textarea', MediaMosaExternalRepositoryObject::PROPERTY_TAGS, Translation :: get('Tags'), array("rows" => "1", "cols" => "80"));
        //$this->addRule(MediaMosaExternalRepositoryObject::PROPERTY_TAGS, Translation :: get('ThisFieldIsRequired'), 'required');

        $this->addElement('textarea', MediaMosaExternalRepositoryObject::PROPERTY_DESCRIPTION, Translation :: get('Description'), array("rows" => "7", "cols" => "110"));
        $this->addElement('text', MediamosaExternalRepositoryObject :: PROPERTY_CREATOR, Translation :: get('Creator'), array("size" => "50"));

        $this->addElement('checkbox', MediamosaExternalRepositoryObject :: PROPERTY_IS_DOWNLOADABLE, Translation :: get('Is downloadable'));

        $this->addElement('hidden', MediamosaExternalRepositoryObject :: PROPERTY_PUBLISHER);
        $this->addelement('hidden', MediamosaExternalRepositoryObject :: PROPERTY_DATE_PUBLISHED);

    }

    function build_creation_form()
    {
        $defaults = array();
            
        $udm = UserDataManager :: get_instance();

        //create values for hidden forms 
        $user = $udm->retrieve_user(Session :: get_user_id());
        $defaults[MediamosaExternalRepositoryObject :: PROPERTY_PUBLISHER] = $user->get_firstname().' '.$user->get_lastname();
        $defaults[MediamosaExternalRepositoryObject :: PROPERTY_DATE_PUBLISHED] = date('Y-m-d H:i:s');

        $this->setDefaults($defaults);

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

    /*
     * creates all necessary objects on mediamosa server to prepare upload
     * -asset
     * -mediafile + acl-rights +medtadata
     * -upload ticket
     * @return array reponse --> asset_id, action, uploadprogress_url
     */
   function prepare_upload()
   {

       $connector = $this->application->get_external_repository_connector();

       //create asset
       if($asset_id = $connector->create_mediamosa_asset())
       {
           if($mediafile_id = $connector->create_mediamosa_mediafile($asset_id, $this->exportValue(MediamosaMediafileObject :: PROPERTY_IS_DOWNLOADABLE)))
           {
                //on success -> add metadata
                $metadata['title'] = $this->exportValue(MediamosaExternalRepositoryObject::PROPERTY_TITLE);
                $metadata['description'] = $this->exportValue(MediamosaExternalRepositoryObject::PROPERTY_DESCRIPTION);
                $metadata['date'] = $this->exportValue(MediamosaExternalRepositoryObject::PROPERTY_DATE_PUBLISHED);
                $metadata['creator'] = $this->exportValue(MediamosaExternalRepositoryObject::PROPERTY_CREATOR);
                $metadata['publisher'] = $this->exportValue(MediamosaExternalRepositoryObject::PROPERTY_PUBLISHER);

                if($connector->add_mediamosa_metadata($asset_id, $metadata))
                {
                    //on success -> get and return ticket
                    if($ticket_response = $connector->create_mediamosa_upload_ticket($mediafile_id))
                    {
                        $ticket_return = array();

                        $ticket_return['asset_id'] = (string) $asset_id;
                        $ticket_return['action'] = (string) $ticket_response->items->item->action;
                        $ticket_return['uploadprogress_url'] = (string) $ticket_response->items->item->uploadprogress_url;

                        return $ticket_return;
                    }
                    //on fail -> rollback
                    else
                    {
                        //remove asset and everything attached
                        $connector->remove_mediamosa_asset($asset_id);
                    }
                }
            }
       }
       return false;
    }

    function update_video_entry($id)
    {
        $connector = $this->application->get_external_repository_connector();
        $data = $this->exportValues();

        if($connector->add_mediamosa_metadata($this->external_repository_object->get_id(), $data))
        {
            return true;
        }
        return false;
    }

}
?>
