<?php
require_once dirname(__FILE__) . '/streaming_video_content_object_table/streaming_video_content_object_table.class.php';

class ExternalRepositoryExporterComponent extends ExternalRepositoryComponent
{

    function run()
    {
        $external_repository_id = Request :: get(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID);
        if (isset($external_repository_id))
        {
            $object = RepositoryDataManager :: get_instance()->retrieve_content_object($external_repository_id);
            $success = $this->export_external_repository_object($object);
            if ($success)
            {
            
            }
        }
        else
        {
            $this->display_header();
            
            $actions = $this->get_default_browser_actions();
            foreach ($actions as $key => $action)
            {
                $actions[$key]['href'] = str_replace('__ID__', '%d', $action['href']);
            }
            $table = new StreamingVideoContentObjectTable($this, $this->get_user(), Document :: get_type_name(), '', $actions);
            echo ($table->as_html());
            $this->display_footer();
        }
    }

    function get_default_browser_actions()
    {
        $browser_actions = array();
        $browser_actions[] = array('href' => $this->get_url(array_merge($this->get_parameters(), array(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID => '__ID__')), false), 'img' => Theme :: get_common_image_path() . 'action_export.png', 'label' => Translation :: get('Export'));
        //$browser_actions[] = array('href' => $this->get_url(array_merge($this->get_parameters(), array(RepoViewer :: PARAM_ACTION => RepoViewer :: ACTION_VIEWER, RepoViewer :: PARAM_ID => '__ID__')), false), 'img' => Theme :: get_common_image_path() . 'action_browser.png', 'label' => Translation :: get('Preview'));        
        return $browser_actions;
    }

    function is_shared_object_browser()
    {
        return false;
    }

    function get_excluded_objects()
    {
        return array();
    }
}
?>