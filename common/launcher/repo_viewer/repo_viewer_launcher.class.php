<?php

class RepoViewerLauncher extends LauncherApplication
{
    const APPLICATION_NAME = 'repo_viewer';
    const PARAM_ELEMENT_NAME = 'element_name';

    function RepoViewerLauncher($user)
    {
        parent :: __construct($user);
    }

    function run()
    {
        $element_name = $this->get_element_name();
        $this->set_parameter(self :: PARAM_ELEMENT_NAME, $element_name);
        
    	if (!RepoViewer::is_ready_to_be_published())
        {
            $repo_viewer = RepoViewer :: construct($this);
            $repo_viewer->set_maximum_select(RepoViewer :: SELECT_SINGLE);
            $repo_viewer->run();
        }
        else
        {
            $this->display_header();
        	
            $object_id = RepoViewer :: get_selected_objects();
            $object = RepositoryDataManager :: get_instance()->retrieve_content_object($object_id, Document :: get_type_name());
            
	        $html = array();
	        $html[] = '<script type="text/javascript">';
	        $html[] = 'window.opener.$("input[name=' . $element_name . '_title]").val("' . addslashes($object->get_title()) . '");';
	        $html[] = 'window.opener.$("input[name=' . $element_name . ']").val("' . addslashes($object->get_id()) . '");';
	        $html[] = 'window.close();';
	        $html[] = '</script>';
	        
	        echo (implode("\n", $html));
	        $this->display_footer();
        }
    }

 	function get_element_name()
    {
        return Request :: get(self :: PARAM_ELEMENT_NAME);
    }
    
    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

    function get_allowed_content_object_types()
    {
		return array(Document :: get_type_name());
    }
}
?>