<?php
/**
 * $Id: user_quota_form.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.forms
 */

class UserQuotaForm extends FormValidator
{

    const RESULT_SUCCESS = 'UserQuotaUpdated';
    const RESULT_ERROR = 'UserQuotaUpdateFailed';

    private $parent;
    private $user;
    private $rdm;
    private $content_object_types;

    /**
     * Creates a new UserQuotaForm
     */
    function UserQuotaForm($user, $action)
    {
        parent :: __construct('quota_settings', 'post', $action);

        $this->user = $user;
        $this->content_object_types = $this->filter_content_object_types();

        $this->build_editing_form();
        $this->setDefaults();
    }

    /**
     * Builds a basic form
     */
    function build_basic_form()
    {
        $this->addElement('category', Translation :: get('GeneralQuota'));
        // Disk Quota
        $this->addElement('text', User :: PROPERTY_DISK_QUOTA, Translation :: get('DiskQuota'), array("size" => "50"));
        $this->addRule(User :: PROPERTY_DISK_QUOTA, Translation :: get('FieldMustBeNumeric'), 'numeric', null, 'server');
        // Database Quota
        $this->addElement('text', User :: PROPERTY_DATABASE_QUOTA, Translation :: get('DatabaseQuota'), array("size" => "50"));
        $this->addRule(User :: PROPERTY_DATABASE_QUOTA, Translation :: get('FieldMustBeNumeric'), 'numeric', null, 'server');
        // Version quota
        $this->addElement('text', User :: PROPERTY_VERSION_QUOTA, Translation :: get('VersionQuota'), array("size" => "50"));
        $this->addRule(User :: PROPERTY_VERSION_QUOTA, Translation :: get('FieldMustBeNumeric'), 'numeric', null, 'server');
        $this->addElement('category');

        $this->addElement('category', Translation :: get('VersionQuota'));
        foreach ($this->content_object_types as $type)
        {
            $this->addElement('text', $type, Translation :: get(Utilities :: underscores_to_camelcase($type)), array("size" => "50"));
            $this->addRule($type, Translation :: get('FieldMustBeNumeric'), 'numeric', null, 'server');
        }
        $this->addElement('category');

        if(PlatformSetting :: get('mediamosa_enabled', 'repository'))
        {
            require_once(Path :: get_application_path(). '/common/streaming_media_manager/type/mediamosa/mediamosa_streaming_media_manager.class.php');
            
            $mdm = MediamosaStreamingMediaDataManager :: get_instance();
            if($mediamosa_servers = $mdm->retrieve_streaming_media_server_objects())
            {
                $this->addElement('category', Translation :: get('MediamosaQuota'));
                while($mediamosa_server = $mediamosa_servers->next_result())
                {
                    //$mediamosa_server = $mdm->retrieve_streaming_media_server_object($mediamosa_quotum->get_server_id());
                    $this->addElement('text', 'mediamosa_' . StreamingMediaUserQuotum :: PROPERTY_SERVER_ID . '_' . $mediamosa_server->get_id(), $mediamosa_server->get_title());
                }
                $this->addElement('category');
            }
        }

        // Submit button
        //$this->addElement('submit', 'quota_settings', 'OK');
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save'), array('class' => 'positive'));
        //$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Builds an editing form
     */
    function build_editing_form()
    {
        $user = $this->user;
        $parent = $this->parent;

        $this->build_basic_form();

        $this->addElement('hidden', User :: PROPERTY_ID);
    }

    /**
     * Updates the quota
     */
    function update_quota()
    {
        $user = $this->user;
        $values = $this->exportValues();
        $failures = 0;
        foreach ($this->content_object_types as $type)
        {
            $userquota = new UserQuota();
            $userquota->set_content_object_type($type);
            $userquota->set_user_quota($values[$type]);
            $userquota->set_user_id($user->get_id());
            if ($values[$type] != '')
            {
                if (! $userquota->update())
                {
                    $failures ++;
                }
            }
        }

        $user->set_version_quota(intval($values[User :: PROPERTY_VERSION_QUOTA]));
        $user->set_database_quota(intval($values[User :: PROPERTY_DATABASE_QUOTA]));
        $user->set_disk_quota(intval($values[User :: PROPERTY_DISK_QUOTA]));
        $user->update();

        if(PlatformSetting :: get('mediamosa_enabled', 'repository'))
        {
            require_once(Path :: get_application_path(). '/common/streaming_media_manager/type/mediamosa/mediamosa_streaming_media_manager.class.php');

            /*
             * if the value doesn't match the default_user_quotum for that server
             * the existing override quotum is updated or a new one is created
             */

            $mdm = MediamosaStreamingMediaDataManager :: get_instance();
            if($mediamosa_quota = $mdm->retrieve_streaming_media_user_quota($user->get_id()))
            {
                $temp_m_quota = array();
                
                while($mediamosa_quotum = $mediamosa_quota->next_result())
                {
                    $temp_m_quota[$mediamosa_quotum->get_server_id()] = $mediamosa_quotum;
                }
            }
            
            if($mediamosa_servers = $mdm->retrieve_streaming_media_server_objects())
            {
                while($mediamosa_server = $mediamosa_servers->next_result())
                {
                    if($values['mediamosa_' . StreamingMediaUserQuotum :: PROPERTY_SERVER_ID . '_' . $mediamosa_server->get_id()] != $mediamosa_server->get_default_user_quotum())
                    {
                        if(isset($temp_m_quota[$mediamosa_server->get_id()]))
                        {
                            
                            $mediamosa_quotum = $temp_m_quota[$mediamosa_server->get_id()];

                            $mediamosa_quotum->set_quotum($values['mediamosa_' . StreamingMediaUserQuotum :: PROPERTY_SERVER_ID . '_' . $mediamosa_quotum->get_server_id()]);

                             if(!$mediamosa_quotum->update())
                             {
                                $failures++;
                             }
                        }
                        else
                        {
                            $mediamosa_quotum = new StreamingMediaUserQuotum();
                            $mediamosa_quotum->set_user_id($user->get_id());
                            $mediamosa_quotum->set_server_id($mediamosa_server->get_id());
                            $mediamosa_quotum->set_quotum($values['mediamosa_' . StreamingMediaUserQuotum :: PROPERTY_SERVER_ID . '_' . $mediamosa_quotum->get_server_id()]);

                            if(!$mediamosa_quotum->create())
                            {
                                $failures++;
                            }
                        }
                    }
                    else
                    {
                        if(isset($temp_m_quota[$mediamosa_server->get_id()]))
                        {
                            $mediamosa_quotum = $temp_m_quota[$mediamosa_server->get_id()];
                            $mediamosa_quotum->delete();
                        }
                    }

                    $connector = new MediamosaStreamingMediaConnector($mediamosa_server->get_id());
                    $connector->set_mediamosa_user_quotum($user->get_id(), $values['mediamosa_' . StreamingMediaUserQuotum :: PROPERTY_SERVER_ID . '_' . $mediamosa_quotum->get_server_id()]);
                }
            }
        }

        if ($failures != 0)
        {
            return false;
        }
        else
        {
            Event :: trigger('quota', 'user', array('target_user_id' => $user->get_id(), 'action_user_id' => $user->get_id()));
            return true;
        }

    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $user = $this->user;
        $defaults[User :: PROPERTY_ID] = $user->get_id();

        $defaults[User :: PROPERTY_VERSION_QUOTA] = $user->get_version_quota();
        $defaults[User :: PROPERTY_DATABASE_QUOTA] = $user->get_database_quota();
        $defaults[User :: PROPERTY_DISK_QUOTA] = $user->get_disk_quota();

        foreach ($this->content_object_types as $type)
        {
            $defaults[$type] = $this->user->get_version_type_quota($type);
        }

        if(PlatformSetting :: get('mediamosa_enabled', 'repository'))
        {
            $mdm = MediamosaStreamingMediaDataManager :: get_instance();

            if($mediamosa_quota = $mdm->retrieve_streaming_media_user_quota($user->get_id()))
            {
                $temp_m_quota = array();
                
                while($mediamosa_quotum = $mediamosa_quota->next_result())
                {
                    $temp_m_quota[$mediamosa_quotum->get_server_id()] = $mediamosa_quotum->get_quotum();
                }
            }

            $mdm = MediamosaStreamingMediaDataManager :: get_instance();

            if($mediamosa_servers = $mdm->retrieve_streaming_media_server_objects())
            {
                while($mediamosa_server = $mediamosa_servers->next_result())
                {
                    //if a specific user quotum is set it will override the default quota for that server
                    if(isset($temp_m_quota[$mediamosa_server->get_id()]))
                    {
                        $defaults['mediamosa_' . StreamingMediaUserQuotum :: PROPERTY_SERVER_ID . '_' . $mediamosa_server->get_id()] = $temp_m_quota[$mediamosa_server->get_id()];
                    }
                    else
                    {
                        $defaults['mediamosa_' . StreamingMediaUserQuotum :: PROPERTY_SERVER_ID . '_' . $mediamosa_server->get_id()] = $mediamosa_server->get_default_user_quotum();
                    }
                }
            }
        }

        parent :: setDefaults($defaults);
    }

    /**
     * Filters learning object types
     */
    function filter_content_object_types()
    {
        $user = $this->user;
        $content_object_types = RepositoryDataManager :: get_registered_types();
        $filtered_object_types = array();

        $hidden_types = array(LearningPathItem :: get_type_name(), PortfolioItem :: get_type_name());

        foreach ($content_object_types as $type)
        {
            $object = ContentObject :: factory($type);
            if ($object instanceof Versionable && ! in_array($object->get_type(), $hidden_types))
            {
                $filtered_object_types[] = $type;
            }
        }

        return $filtered_object_types;
    }
}
?>