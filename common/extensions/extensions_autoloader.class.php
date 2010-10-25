<?php
namespace common\extensions;
use common\libraries;
use common\libraries\Utilities;
use common\libraries\Path;

/**
 * $Id: extensions_autoloader.class.php 236 2009-11-16 12:56:59Z scaramanga $
 * @author vanpouckesven
 * @package application.common
 */

class ExtensionsAutoloader
{
    public static $class_name;

    static function load($classname)
    {
        if (self :: check_for_external_repository_files($classname))
        {
            return true;
        }
        if (self :: check_for_feedback_manager($classname))
        {
            return true;
        }
        
        if (self :: check_for_invitation_manager($classname))
        {
            return true;
        }
        if (self :: check_for_repo_viewer($classname))
        {
            return true;
        }
        if (self :: check_for_reporting_viewer($classname))
        {
            return true;
        }
        if (self :: check_for_external_repository_manager($classname))
        {
            return true;
        }
        
        if (self :: check_for_dynamic_form_manager($classname))
        {
            return true;
        }
        
        if (self :: check_for_rights_editor_manager($classname))
        {
            return true;
        }
        
        if (self :: check_for_validation_manager($classname))
        {
            return true;
        }
        
        if (self :: check_for_email_manager($classname))
        {
            return true;
        }
        
        if (self :: check_for_category_manager($classname))
        {
            return true;
        }
        
        return false;
    }

    static function check_for_external_repository_files($classname)
    {
        $classname = Utilities :: get_namespace_classname('common\extensions\external_repository_manager', $classname);
        if ($classname)
        {
            $list = array(
                    'external_repository_object' => 'external_repository_object.class.php', 'external_repository_object_display' => 'external_repository_object_display.class.php', 
                    'external_repository_component' => 'external_repository_component.class.php', 'external_repository_connector' => 'external_repository_connector.class.php', 
                    'external_repository_menu' => 'external_repository_menu.class.php', 'external_repository_object_renderer' => 'external_repository_object_renderer.class.php', 
                    'default_external_repository_object_table_data_provider' => 'table/default_external_repository_object_table_data_provider.class.php', 
                    'default_external_repository_object_table_column_model' => 'table/default_external_repository_object_table_column_model.class.php', 
                    'default_external_repository_object_table_cell_renderer' => 'table/default_external_repository_object_table_cell_renderer.class.php', 
                    'default_external_repository_gallery_object_table_property_model' => 'table/default_external_repository_gallery_object_table_property_model.class.php', 
                    'default_external_repository_gallery_object_table_data_provider' => 'table/default_external_repository_gallery_object_table_data_provider.class.php', 
                    'default_external_repository_gallery_object_table_cell_renderer' => 'table/default_external_repository_gallery_object_table_cell_renderer.class.php', 
                    'external_repository_browser_gallery_property_model' => 'component/external_repository_browser_gallery_table/external_repository_browser_gallery_table_property_model.class.php');
            
            $lower_case = Utilities :: camelcase_to_underscores($classname);
            
            if (key_exists($lower_case, $list))
            {
                $url = $list[$lower_case];
                require_once Path :: get_common_extensions_path() . 'external_repository_manager/php/' . $url;
                return true;
            }
        }
        
        return false;
    }

    static function check_for_feedback_manager($classname)
    {
        $classname = Utilities :: get_namespace_classname('common\extensions\feedback_manager', $classname);
        if ($classname)
        {
            $list = array('feedback_manager' => 'feedback_manager.class.php');
            $lower_case = Utilities :: camelcase_to_underscores($classname);
            
            if (key_exists($lower_case, $list))
            {
                $url = $list[$lower_case];
                require_once Path :: get_common_extensions_path() . 'feedback_manager/php/' . $url;
                return true;
            }
        }
        return false;
    }

    static function check_for_invitation_manager($classname)
    {
        $classname = Utilities :: get_namespace_classname('common\extensions\invitation_manager', $classname);
        if ($classname)
        {
            $list = array(
                    'invitation_manager' => 'invitation_manager.class.php', 'invitation' => 'invitation.class.php', 'invitation_form' => 'invitation_form.class.php', 'invitation_support' => 'invitation_support.class.php', 
                    'invitation_parameters' => 'invitation_parameters.class.php');
            $lower_case = Utilities :: camelcase_to_underscores($classname);
            
            if (key_exists($lower_case, $list))
            {
                $url = $list[$lower_case];
                require_once Path :: get_common_extensions_path() . 'invitation_manager/php/' . $url;
                return true;
            }
        }
        return false;
    }

    static function check_for_repo_viewer($classname)
    {
        $classname = Utilities :: get_namespace_classname('common\extensions\repo_viewer', $classname);
        if ($classname)
        {
            $list = array('repo_viewer' => 'repo_viewer.class.php', 'repo_viewer_interface' => 'repo_viewer_interface.class.php');
            $lower_case = Utilities :: camelcase_to_underscores($classname);
            
            if (key_exists($lower_case, $list))
            {
                $url = $list[$lower_case];
                require_once Path :: get_common_extensions_path() . 'repo_viewer/php/' . $url;
                return true;
            }
        }
        return false;
    }

    static function check_for_reporting_viewer($classname)
    {
        $classname = Utilities :: get_namespace_classname('common\extensions\reporting_viewer', $classname);
        if ($classname)
        {
            $list = array('reporting_viewer' => 'reporting_viewer.class.php');
            $lower_case = Utilities :: camelcase_to_underscores($classname);
            
            if (key_exists($lower_case, $list))
            {
                $url = $list[$lower_case];
                require_once Path :: get_common_extensions_path() . 'reporting_viewer/php/' . $url;
                return true;
            }
        }
        return false;
    }

    static function check_for_external_repository_manager($classname)
    {
        $classname = Utilities :: get_namespace_classname('common\extensions\external_repository_manager', $classname);
        if ($classname)
        {
            $list = array('external_repository_manager' => 'external_repository_manager.class.php');
            $lower_case = Utilities :: camelcase_to_underscores($classname);
            
            if (key_exists($lower_case, $list))
            {
                $url = $list[$lower_case];
                require_once Path :: get_common_extensions_path() . 'external_repository_manager/php/' . $url;
                return true;
            }
        }
        return false;
    }

    static function check_for_dynamic_form_manager($classname)
    {
        $classname = Utilities :: get_namespace_classname('common\extensions\dynamic_form_manager', $classname);
        if ($classname)
        {
            $list = array('dynamic_form_manager' => 'dynamic_form_manager.class.php');
            $lower_case = Utilities :: camelcase_to_underscores($classname);
            
            if (key_exists($lower_case, $list))
            {
                $url = $list[$lower_case];
                require_once Path :: get_common_extensions_path() . 'dynamic_form_manager/php/' . $url;
                return true;
            }
        }
        return false;
    }

    static function check_for_rights_editor_manager($classname)
    {
        $classname = Utilities :: get_namespace_classname('common\extensions\rights_editor_manager', $classname);
        if ($classname)
        {
            $list = array('rights_editor_manager' => 'rights_editor_manager.class.php');
            $lower_case = Utilities :: camelcase_to_underscores($classname);
            
            if (key_exists($lower_case, $list))
            {
                $url = $list[$lower_case];
                require_once Path :: get_common_extensions_path() . 'rights_editor_manager/php/' . $url;
                return true;
            }
        }
        return false;
    }

    static function check_for_validation_manager($classname)
    {
        $classname = Utilities :: get_namespace_classname('common\extensions\validation_manager', $classname);
        if ($classname)
        {
            $list = array('validation_manager' => 'validation_manager.class.php');
            $lower_case = Utilities :: camelcase_to_underscores($classname);
            
            if (key_exists($lower_case, $list))
            {
                $url = $list[$lower_case];
                require_once Path :: get_common_extensions_path() . 'validation_manager/php/' . $url;
                return true;
            }
        }
        return false;
    }

    static function check_for_email_manager($classname)
    {
        $classname = Utilities :: get_namespace_classname('common\extensions\email_manager', $classname);
        if ($classname)
        {
            $list = array('email_manager' => 'email_manager.class.php');
            $lower_case = Utilities :: camelcase_to_underscores($classname);
            
            if (key_exists($lower_case, $list))
            {
                $url = $list[$lower_case];
                require_once Path :: get_common_extensions_path() . 'email_manager/php/' . $url;
                return true;
            }
        }
        return false;
    }

    //    static function check_for_video_conferencing_manager($classname)
    //    {
    //        $classname = Utilities :: get_namespace_classname('common\extensions\video_conferencing_manager', $classname);
    //        if ($classname)
    //        {
    //            $list = array('video_conferencing_manager' => 'video_conferencing_manager.class.php', 
    //                'video_conferencing_meeting_object' => 'video_conferencing_meeting_object.class.php', 
    //                'video_conferencing_participants_object' => 'video_conferencing_participants_object.class.php', 
    //                'video_conferencing_connector' => 'video_conferencing_connector.class.php',
    //            	'video_conferencing_menu' => 'video_conferencing_menu.class.php', 
    //                'video_conferencing_meeting_object_renderer' => 'video_conferencing_meeting_object_renderer.class.php', 
    //                'video_conferencing_participants_object_renderer' => 'video_conferencing_participants_object_renderer.class.php', 
    //                'video_conferencing_meeting_object_display' => 'video_conferencing_meeting_object_renderer.class.php', 
    //                'video_conferencing_participants_object_display' => 'video_conferencing_participants_object_renderer.class.php');
    //            $lower_case = Utilities :: camelcase_to_underscores($classname);
    //            
    //            if (key_exists($lower_case, $list))
    //            {
    //                $url = $list[$lower_case];
    //                require_once Path :: get_common_extensions_path() . 'video_conferencing_manager/php/' . $url;
    //                return true;
    //            }
    //        }
    //        return false;
    //    } 
    

    static function check_for_category_manager($classname)
    {
        $classname = Utilities :: get_namespace_classname('common\extensions\category_manager', $classname);
        if ($classname)
        {
            $list = array('category_manager' => 'category_manager.class.php', 
            		'test_category_manager' => 'test_category_manager.class.php');
            $lower_case = Utilities :: camelcase_to_underscores($classname);
            
            if (key_exists($lower_case, $list))
            {
                $url = $list[$lower_case];
                require_once Path :: get_common_extensions_path() . 'category_manager/php/' . $url;
                return true;
            }
        }
        return false;
    }
}

?>