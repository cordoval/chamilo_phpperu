<?php

/**
 * $Id: user_quota_form.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.forms
 */
require_once Path :: get_repository_path() . '/lib/external_repository_user_quotum.class.php';
require_once dirname(__FILE__) . "/../../../application/common/external_repository_manager/external_repository_connector.class.php";


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

        $rdm = RepositoryDataManager :: get_instance();
        
        if(count($this->get_active_external_repositories()))
        {
            $this->addElement('category', Translation :: get('ExternalRepositories'));

            foreach($this->active_external_repositories as $repository)
            {
                $this->addElement('text', ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY . '_' . $repository['data']->get_id(), $repository['data']->get_title());
            }
            $this->addElement('category');
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

        $rdm = RepositoryDataManager :: get_instance();
        

        foreach($this->get_active_external_repositories() as $repository)
        {
            if($values[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY . '_' . $repository['data']->get_id()] != $repository['settings']->get_value())
            {
                $connector = ExternalRepositoryConnector :: get_instance($repository['data']);
                
                if($user_quotum = $rdm->retrieve_external_repository_user_quotum($user->get_id(), $repository['data']->get_id()))
                {
                   $user_quotum->set_quotum($values[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY . '_' . $repository['data']->get_id()]);

                    if(!$user_quotum->update())
                    {
                        $failures ++;
                    }
                    else
                    {
                        $connector->set_mediamosa_user_quotum($user->get_id(),$values[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY . '_' . $repository['data']->get_id()]);
                    }
                }
                else
                {
                    $user_quotum = new ExternalRepositoryUserQuotum();
                    $user_quotum->set_user_id($user->get_id());
                    $user_quotum->set_external_repository_id($repository['data']->get_id());
                    $user_quotum->set_quotum($values[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY . '_' . $repository['data']->get_id()]);

                    if(!$user_quotum->create())
                    {
                        $failures ++;
                    }
                    else
                    {
                        $connector->set_mediamosa_user_quotum($user->get_id(),$values[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY . '_' . $repository['data']->get_id()]);
                    }
                }
            }
            else
            {
                if($user_quotum = $rdm->retrieve_external_repository_user_quotum($user->get_id(), $repository['data']->get_id()))
                {
                    $user_quotum->set_quotum($values[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY . '_' . $repository['data']->get_id()]);

                    if(!$user_quotum->delete())
                    {
                        $failures ++;
                    }
                    else
                    {
                        $connector->set_mediamosa_user_quotum($user->get_id(),$values[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY . '_' . $repository['data']->get_id()]);
                    }
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

        $rdm = RepositoryDataManager :: get_instance();

        foreach($this->get_active_external_repositories() as $repository)
        {
            if($user_quotum = $rdm->retrieve_external_repository_user_quotum($user->get_id(), $repository['settings']->get_external_repository_id()))
           
            {
                $defaults[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY . '_' . $repository['settings']->get_external_repository_id()] = $user_quotum->get_quotum();
            }
            else
            {
                $defaults[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY . '_' . $repository['settings']->get_external_repository_id()] = $repository['settings']->get_value();
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

    function get_active_external_repositories()
    {
        if(! $this->active_repositories_searched)
        {
            $rdm = RepositoryDataManager :: get_instance();

            $condition2 = new EqualityCondition(ExternalRepositorySetting :: PROPERTY_VARIABLE, ExternalRepositoryManager :: PARAM_USER_QUOTUM);
            $settings = $rdm->retrieve_external_repository_settings($condition2);
            while($setting = $settings->next_result())
            {
                $tempSettings[$setting->get_external_repository_id()] = $setting;
            }

            $condition = new EqualityCondition(ExternalRepository :: PROPERTY_ENABLED, 1);
            $active_external_repositories = $rdm->retrieve_external_repositories($condition);

            while($active_external_repository = $active_external_repositories->next_result())
            {
                if($tempSettings[$active_external_repository->get_id()])
                {
                    $this->active_external_repositories[$active_external_repository->get_id()]['settings'] = $tempSettings[$active_external_repository->get_id()];
                    $this->active_external_repositories[$active_external_repository->get_id()]['data'] = $active_external_repository;
                }
            }
            
            $this->active_repositories_searched = true;
        }
        return $this->active_external_repositories;
    }
}
?>