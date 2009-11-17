<?php

/**
 * $Id: application.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.package_remover.type
 */
class PackageApplicationRemover extends PackageRemover
{
    private $registration;

    function run()
    {
        $adm = AdminDataManager :: get_instance();
        $registration = $adm->retrieve_registration($this->get_package());
        $this->registration = $registration;
        
        // Deactivate the application, thus making it inaccesible
        $this->add_message(Translation :: get('DeactivatingApplication'));
        $registration->toggle_status();
        if (! $registration->update())
        {
            return $this->installation_failed('initilization', Translation :: get('ApplicationDeactivationFailed'));
        }
        else
        {
            $mdm = MenuDataManager :: get_instance();
            $this->add_message(Translation :: get('RemovingMenuItems'));
            
            $condition = new EqualityCondition(NavigationItem :: PROPERTY_APPLICATION, $registration->get_name());
            if ($mdm->delete_navigation_items($condition))
            {
                $this->installation_successful('initilization', Translation :: get('ApplicationSuccessfullyDeactivated'));
            }
            else
            {
                return $this->installation_failed('initilization', Translation :: get('ApplicationDeactivationFailed'));
            }
        }
        
        // Remove webservices
        if (! $this->remove_webservices())
        {
            return $this->installation_failed('webservice', Translation :: get('WebservicesDeletionFailed'));
        }
        else
        {
            $this->installation_successful('webservice', Translation :: get('WebservicesSuccessfullyDeleted'));
        }
        
        // Remove reporting
        if (! $this->remove_reporting())
        {
            return $this->installation_failed('reporting', Translation :: get('ReportingDeletionFailed'));
        }
        else
        {
            $this->installation_successful('reporting', Translation :: get('ReportingSuccessfullyDeleted'));
        }
        
        // Remove tracking
        if (! $this->remove_tracking())
        {
            return $this->installation_failed('tracking', Translation :: get('TrackingDeletionFailed'));
        }
        else
        {
            $this->installation_successful('tracking', Translation :: get('TrackingSuccessfullyDeleted'));
        }
        
        // Remove roles and rights
        if (! $this->remove_rights())
        {
            return $this->installation_failed('rights', Translation :: get('RightsDeletionFailed'));
        }
        else
        {
            $this->installation_successful('rights', Translation :: get('RightsSuccessfullyDeleted'));
        }
        
        // Remove storage units
        if (! $this->remove_storage_units())
        {
            return $this->installation_failed('database', Translation :: get('StorageUnitsDeletionFailed'));
        }
        else
        {
            $this->installation_successful('database', Translation :: get('StorageUnitsSuccessfullyDeleted'));
        }
        
        // Remove application
        if (! $this->remove_settings() || ! $this->remove_application())
        {
            return $this->installation_failed('failed', Translation :: get('ApplicationDeletionFailed'));
        }
        else
        {
            $this->installation_successful('finished', Translation :: get('ApplicationSuccessfullyDeleted'));
        }
        
        return true;
    }

    function remove_webservices()
    {
        $registration = $this->registration;
        
        $wdm = WebserviceDataManager :: get_instance();
        $condition = new EqualityCondition(WebserviceRegistration :: PROPERTY_APPLICATION, $registration->get_name());
        $webservices = $wdm->retrieve_webservices($condition);
        
        while ($webservice = $webservices->next_result())
        {
            $message = Translation :: get('RemovingWebserviceRegistration') . ': ' . $webservice->get_name();
            $this->add_message($message);
            if (! $webservice->delete())
            {
                return false;
            }
        }
        
        // TODO: Delete categories added  by the application
        

        return true;
    }

    function remove_reporting()
    {
        $registration = $this->registration;
        
        $rdm = ReportingDataManager :: get_instance();
        
        $this->add_message(Translation :: get('RemovingReportingblocks'));
        $condition = new EqualityCondition(ReportingBlock :: PROPERTY_APPLICATION, $registration->get_name());
        if (! $rdm->delete_reporting_blocks($condition))
        {
            return false;
        }
        
        $this->add_message(Translation :: get('RemovingReportingTemplates'));
        $condition = new EqualityCondition(ReportingTemplateRegistration :: PROPERTY_APPLICATION, $registration->get_name());
        if (! $rdm->delete_reporting_template_registrations($condition))
        {
            return false;
        }
        
        if (! $rdm->delete_orphaned_block_template_relations())
        {
            $this->add_message(Translation :: get('DeletingOrphanedBlockTemplateRelationsFailed'), self :: TYPE_WARNING);
        }
        else
        {
            $this->add_message(Translation :: get('DeletingOrphanedBlockTemplateRelations'));
        }
        
        return true;
    }

    function remove_tracking()
    {
        $registration = $this->registration;
        $base_path = Path :: get_application_path() . 'lib/' . $registration->get_name() . '/trackers/tracker_tables/';
        
        $database = new Database();
        $database->set_prefix('tracking_');
        
        if (is_dir($base_path))
        {
            $files = Filesystem :: get_directory_content($base_path, Filesystem :: LIST_FILES);
            
            if (count($files) > 0)
            {
                foreach ($files as $file)
                {
                    if ((substr($file, - 3) == 'xml'))
                    {
                        $doc = new DOMDocument();
                        $doc->load($file);
                        $object = $doc->getElementsByTagname('object')->item(0);
                        $name = $object->getAttribute('name');
                        
                        $this->add_message(Translation :: get('DroppingTrackingStorageUnit') . ': <em>' . $name . '</em>');
                        if (! $database->drop_storage_unit($name))
                        {
                            return false;
                        }
                    }
                }
            }
        }
        
        $condition = new EqualityCondition(Event :: PROPERTY_BLOCK, $registration->get_name());
        $tdm = TrackingDataManager :: get_instance();
        $this->add_message(Translation :: get('DeletingApplicationEvents'));
        if (! $tdm->delete_events($condition))
        {
            return false;
        }
        
        $tracker_path = 'application/lib/' . $registration->get_name() . '/trackers/';
        $condition = new EqualityCondition(TrackerRegistration :: PROPERTY_PATH, $tracker_path);
        $this->add_message(Translation :: get('DeletingApplicationTrackerRegistrations'));
        if (! $tdm->delete_tracker_registrations($condition))
        {
            return false;
        }
        
        if (! $tdm->delete_orphaned_event_rel_tracker())
        {
            $this->add_message(Translation :: get('DeletingOrphanedEventRelTrackersFailed'), self :: TYPE_WARNING);
        }
        
        return true;
    }

    function remove_rights()
    {
        $registration = $this->registration;
        $rdm = RightsDataManager :: get_instance();
        $condition = new EqualityCondition(Location :: PROPERTY_APPLICATION, $registration->get_name());
        $this->add_message(Translation :: get('DeletingApplicationLocations'));
        if (! $rdm->delete_locations($condition))
        {
            return false;
        }
        else
        {
            if (! $rdm->delete_orphaned_rights_template_right_locations())
            {
                $this->add_message(Translation :: get('DeletingOrphanedRoleRightLocationsFailed'), self :: TYPE_WARNING);
            }
            
            return true;
        }
    }

    function remove_settings()
    {
        $registration = $this->registration;
        $adm = AdminDataManager :: get_instance();
        $condition = new EqualityCondition(Setting :: PROPERTY_APPLICATION, $registration->get_name());
        
        $this->add_message(Translation :: get('DeletingApplicationSettings'));
        return $adm->delete_settings($condition);
    }

    function remove_storage_units()
    {
        $registration = $this->registration;
        $database = new Database();
        $database->set_prefix($registration->get_name() . '_');
        
        $path = Path :: get_application_path() . 'lib/' . $registration->get_name() . '/install/';
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

    function remove_application()
    {
        $registration = $this->registration;
        
        $this->add_message(Translation :: get('DeletingApplicationRegistration'));
        if (! $registration->delete())
        {
            return false;
        }
        
        //        $this->add_message(Translation :: get('DeletingApplication'));
        //        $path = Path :: get_application_path() . 'lib/' . $registration->get_name() . '/';
        //        if (! Filesystem :: remove($path))
        //        {
        //            return false;
        //        }
        

        return true;
    }
}
?>