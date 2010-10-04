<?php

/**
 * $Id: content_object.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.package_remover.type
 */
class PackageContentObjectRemover extends PackageRemover
{
    private $registration;

    function run()
    {
        $adm = AdminDataManager :: get_instance();
        $registration = $adm->retrieve_registration($this->get_package());
        $this->registration = $registration;

        // Check dependencies before doing anything at all
        if (! $this->check_dependencies())
        {
            return $this->installation_failed('failed', Translation :: get('OtherPackagesDependOnThisPackage'));
        }
        else
        {
            $this->installation_successful('dependencies', Translation :: get('NoConflictingDependencies'));
        }

        // Deactivate the learning object, thus making it inaccesible
        if (! $this->deactivate_content_object_type())
        {
            return $this->installation_failed('failed', Translation :: get('ContentObjectDeactivationFailed'));
        }
        else
        {
            $this->installation_successful('initilization', Translation :: get('ContentObjectSuccessfullyDeactivated'));
        }

        if (! $this->delete_content_objects())
        {
            return $this->installation_failed('failed', Translation :: get('ContentObjectDeletionFailed'));
        }
        else
        {
            $this->installation_successful('repository', Translation :: get('ContentObjectSuccessfullyDeleted'));
        }

        if (! $this->remove_storage_units())
        {
            return $this->installation_failed('failed', Translation :: get('StorageUnitsDeletionFailed'));
        }
        else
        {
            $this->installation_successful('database', Translation :: get('StorageUnitsSuccessfullyDeleted'));
        }

        if (! $this->remove_content_object())
        {
            return $this->installation_failed('failed', Translation :: get('ObjectDeletionFailed'));
        }
        else
        {
            $this->installation_successful('finished', Translation :: get('ObjectSuccessfullyDeleted'));
        }

        return true;
    }

    function deactivate_content_object_type()
    {
        $registration = $this->registration;

        $this->add_message(Translation :: get('DeactivatingContentObject'));
        $registration->toggle_status();
        if (! $registration->update())
        {
            return false;
        }

        $this->add_message(Translation :: get('DisablingContentObjectCreation'));
        $adm = AdminDataManager :: get_instance();
        $setting = $adm->retrieve_setting_from_variable_name('allow_' . $registration->get_name() . '_creation', RepositoryManager :: APPLICATION_NAME);
        if ($setting)
        {
            if (! $setting->delete())
            {
                return false;
            }
        }

        return true;
    }

    function delete_content_objects()
    {
        $registration = $this->registration;

        $rdm = RepositoryDataManager :: get_instance();
        $content_objects = $rdm->retrieve_type_content_objects($registration->get_name());

        while ($content_object = $content_objects->next_result())
        {
            $this->add_message(Translation :: get('DeletingObject') . ': <em>' . $content_object->get_title() . '</em>');
            $versions = $content_object->get_content_object_versions();

            foreach ($versions as $version)
            {
                if (! $version->delete_links())
                {
                    return false;
                }

                if (! $version->delete())
                {
                    return false;
                }
            }
        }

        return true;
    }

    function remove_storage_units()
    {
        $registration = $this->registration;
        $database = new Database();
        $database->set_prefix(RepositoryManager :: APPLICATION_NAME . '_');

        $path = Path :: get_repository_path() . 'lib/content_object/' . $registration->get_name() . '/';
        $files = Filesystem :: get_directory_content($path, Filesystem :: LIST_FILES);

        foreach ($files as $file)
        {
            if ((substr($file, - 3) == 'xml'))
            {
                $doc = new DOMDocument();
                $doc->load($file);
                $object = $doc->getElementsByTagname('object')->item(0);
                $name = $object->getAttribute('name');

                $this->add_message(Translation :: get('DroppingStorageUnit') . ': <em>' . $name . '</em>');

                if (! $database->drop_storage_unit($name))
                {
                    return false;
                }
            }
        }

        return true;
    }

    function remove_content_object()
    {
        $registration = $this->registration;

        $this->add_message(Translation :: get('DeletingContentObjectRegistration'));
        if (! $registration->delete())
        {
            return false;
        }

        //        $this->add_message(Translation :: get('DeletingContentObject'));
        //        $path = Path :: get_reporting_path() . 'lib/content_object/' . $registration->get_name() . '/';
        //        if (!Filesystem :: remove($path))
        //        {
        //            return false;
        //        }


        return true;
    }
}
?>