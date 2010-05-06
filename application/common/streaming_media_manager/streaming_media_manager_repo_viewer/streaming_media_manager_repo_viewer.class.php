<?php
//require_once dirname(__FILE__) . '/component/content_object_table/content_object_table.class.php';
require_once dirname(__FILE__) . '/streaming_media_manager_repo_viewer_component.class.php';

class StreamingMediaManagerRepoViewer extends RepoViewer
{
    function StreamingMediaManagerRepoViewer($parent, $types, $maximum_select = RepoViewer :: SELECT_MULTIPLE, $excluded_objects = array(), $parse_input = true)
    {
    	parent :: __construct($parent, $types, $maximum_select, $excluded_objects, $parse_input);
        
        $this->set_types(Document :: get_type_name());
    }

    function get_repo_viewer_component($action)
    {
        return StreamingMediaManagerRepoViewerComponent :: factory($action, $this);
    }
}
?>