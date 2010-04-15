<?php
/**
 * Description of StreamingVideoClipManager class
 *
 * @author jevdheyd
 */
class StreamingVideoClipManager extends RepositoryManager {

    const PARAM_STREAMING_VIDEO_APPLICATION = 'sv_application';
    
    function create($create_form = null){
       //is a streaming video application selected?
       if(is_null(Request :: get(self :: PARAM_STREAMING_VIDEO_APPLICATION)))
       {
            //get all streaming video apps
            //identified by STREAMING_VIDEO_ADMIN_SETTING parameter set to true

            $conditions[] = new EqualityCondition(Setting :: PROPERTY_VARIABLE, StreamingVideoClip :: STREAMING_VIDEO_ADMIN_SETTING);
            $conditions[] = new EqualityCondition(Setting :: PROPERTY_VALUE, StreamingVideoClip :: STREAMING_VIDEO_ADMIN_SETTING_VALUE);
            $and_condition = new AndCondition($conditions);

            $adm = AdminDataManager :: get_instance();
            $result = $adm->retrieve_settings($and_condition);

            if($result->size() == 0)
            {
                //error
            }
            elseif($result->size() == 1)
            {
                $appname = Setting::PROPERTY_APPLICATION;
                //set app
                $content_object->set_application($result->next_result->$appname);
            }
            else
            {
                $applications = array();
                while($setting = $result->next_result())
                {
                    $applications[$setting->get_application()] = $setting->get_application();
                }
                $extra_params = array();
                if(is_null($create_form))
                {
                    $type_form = new FormValidator('select_streaming_video_app', 'post', $this->get_url($extra_params));
                }else
                {
                    $type_form = $create_form;
                    $type_form->action='';
                }
                
                 //application select form
                
                if($type_form->validate())
                {
                    //set app
                    $content_object->set_application($type_form->getSubmitValue(self::PARAM_STREAMING_VIDEO_APPLICATION));

                }
                else
                {
                    $type_form->addElement('select', self :: PARAM_STREAMING_VIDEO_APPLICATION, Translation :: get('CreateStreamingVideoClip'), $applications);
                    //$type_form->display();
                }
            }
       }
            //if the application property is set
        if(!is_null(Request :: get(self :: PARAM_STREAMING_VIDEO_APPLICATION)))
        {
            $the_class_name = $streaming_video_clip->get_application().'StreamingVideoClipForm';
            $streaming_video_clip_form = new $the_class_name();

            if($streaming_video_clip_form->validate())
            {
                $streaming_video_clip_form->create_content_object();
            }
            else
            {
                $streaming_video_clip_form->build_creation_form();
            }
        }
    }

    function run()
    {
        $trail = new BreadcrumbTrail(false);
        $trail->add_help('repository general');
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Create')));
        $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE => $type)), Translation :: get(ContentObject :: type_to_class($type) . 'CreationFormTitle')));
        $this->display_header($trail, false, true);

        //create new content object
        $streaming_video_clip = new StreamingVideoClip();
        
        //first the application needs to be determined
        if(is_null($streaming_video_clip->get_application()))
        {
            //get all streaming video apps
            //identified by STREAMING_VIDEO_ADMIN_SETTING parameter set to true

            $condition1 = new EqualityCondition('variable', StreamingVideoClip :: STREAMING_VIDEO_ADMIN_SETTING);
            $condition2 = new EqualityCondition('value', StreamingVideoClip :: STREAMING_VIDEO_ADMIN_SETTING_VALUE);
            $and_condition = new AndCondition('$condition1', '$condition2');

            $adm = AdminDataManager :: get_instance();
            $result = $adm->retrieve_settings($and_condition);
            
            $applications = array();

            while($setting = $result->next_result())
            {
                $applications[] = $setting->get_application();
            }
            //TODO: jens --> find out correct way of determining if no records are found
            if(count($applications) == 0)
            {
                //error message
            }
            //if there are multiple streaming_video_apps
            elseif(count($applications) > 1)
            {
                //application select form
                $select_form = new FormValidator('select_application');
                $select_form->addElement('select',StreamingVideoClip :: PROPERTY_APPLICATION);

                if($select_form->validate())
                {
                    //set app
                    $streaming_video_clip->set_application($applications[0]);
                }
            }
            //if n=1 selection form is not needed
            else
            {
                //set app
                $streaming_video_clip->set_application($applications[0]);
            }
        }
        
        //if the application property is set
        if(!is_null($streaming_video_clip->get_application()))
        {
            $the_class_name = $streaming_video_clip->get_application().'StreamingVideoClipForm';
            $streaming_video_clip_form = new $the_class_name();

            if($streaming_video_clip_form->validate())
            {
                $streaming_video_clip_form->create_content_object();
            }
            else
            {
                $streaming_video_clip_form->build_creation_form();
            }
        }
    }
}
?>
