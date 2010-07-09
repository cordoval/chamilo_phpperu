<?php

/**
 * Description of StreamingVideoClipForm class
 *
 * @author jevdheyd
 */

require_once Path :: get_application_path() . 'common/streaming_media_manager/type/mediamosa/mediamosa_streaming_media_connector.class.php';

class StreamingVideoClipForm extends ContentObjectForm {

    function streaming_video_clip_form_elements()
    {
        $link = PATH :: get_launcher_application_path(true) . 'index.php?' . Application::PARAM_APPLICATION . '=' . StreamingMediaLauncher::APPLICATION_NAME .'&'. StreamingMediaManager::PARAM_TYPE . '=' . 'mediamosa';

        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->addElement('hidden', StreamingVideoClip :: PROPERTY_SERVER_ID);
        $this->addElement('hidden', StreamingVideoClip :: PROPERTY_ASSET_ID);
        $this->addElement('hidden', StreamingVideoClip :: PROPERTY_PUBLISHER);
        $this->addElement('hidden', StreamingVideoClip :: PROPERTY_CREATOR);
        $this->addElement('static', null, null, '<a class="button normal_button upload_button" onclick="javascript:openPopup(\''. $link .'\');"> '. Translation :: get('BrowseStreamingVideo') .'</a>');
        $this->addElement('category');

    }

    function build_creation_form()
    {
        parent :: build_creation_form();

        $this->streaming_video_clip_form_elements();
    }

    function build_editing_form(){
        parent :: build_editing_form();

        $this->streaming_video_clip_form_elements();
    }

    function create_content_object()
    {
        $object = new StreamingVideoClip();

        $object->set_server_id($this->exportValue(StreamingVideoClip :: PROPERTY_SERVER_ID));
        $object->set_asset_id($this->exportValue(StreamingVideoClip :: PROPERTY_ASSET_ID));

        $this->set_content_object($object);

        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $object = $this->get_content_object();

        $object->set_server_id($this->exportValue(StreamingVideoClip :: PROPERTY_SERVER_ID));
        $object->set_asset_id($this->exportValue(StreamingVideoClip :: PROPERTY_ASSET_ID));

        //xdebug_break();
        //TODO: implement in rights-manager-component
        $this->edit_rights();

        return parent :: update_content_object();
    }

    function edit_rights()
    {
        $object = $this->get_content_object();
        $rdm = RightsDataManager :: get_instance();
        $mmc = new MediamosaStreamingMediaConnector($object->get_server_id());
        $rights = array();

        $location = RepositoryRights :: get_location_id_by_identifier('content_object', $object->get_id(), Session :: get_user_id(), 'user_tree');

        $condition1 = new EqualityCondition(UserRightLocation :: PROPERTY_LOCATION_ID, $location);
        $condition2 = new EqualityCondition(UserRightLocation :: PROPERTY_RIGHT_ID, RepositoryRights :: VIEW_RIGHT);
        $condition3 = new EqualityCondition(UserRightLocation :: PROPERTY_VALUE, 1);

        $condition = new AndCondition($condition1, $condition2, $condition3);
        $update = false;
        
        //users
        $rights_users = $rdm->retrieve_user_right_locations($condition);
        
        if($rights_users)
        {
            while($rights_user = $rights_users->next_result())
            {
                $rights['aut_user'][] = $rights_user->get_user_id();
                $update = true;
            }
        }

        //groups
        $rights_groups = $rdm->retrieve_group_right_locations($condition);

        if($rights_groups)
        {
            while($rights_group = $rights_groups->next_result())
            {
                $rights['aut_group'][] = $rights_group->get_group_id();
                $update = true;
            }
        }

        //update mediamosa
        if($update) $mmc->set_mediamosa_asset_rights($object->get_asset_id(), $rights, $object->get_owner_id());
    }

    function setDefaults($defaults = array())
    {
        $content_object = $this->get_content_object();

        $defaults[StreamingVideoClip :: PROPERTY_ASSET_ID] = $content_object->get_asset_id();
        $defaults[StreamingVideoClip :: PROPERTY_SERVER_ID] = $content_object->get_server_id();
        $defaults[StreamingVideoClip :: PROPERTY_PUBLISHER] = $content_object->get_publisher();
        $defaults[StreamingVideoClip :: PROPERTY_CREATOR] = $content_object->get_creator();

        parent :: setDefaults($defaults);
    }
}
?>