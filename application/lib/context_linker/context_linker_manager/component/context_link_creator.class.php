<?php


/**
 * @package application.context_linker.context_linker.component
 */
require_once dirname(__FILE__).'/../context_linker_manager.class.php';
require_once dirname(__FILE__) . '/../../../metadata/metadata_data_manager.class.php';
require_once dirname(__FILE__) . '/../../../metadata/metadata_manager/metadata_manager.class.php';

/**
 * Component to create a new context_link object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class ContextLinkerManagerContextLinkCreatorComponent extends ContextLinkerManager
{
    /**
     * Runs this component and displays its output.
     */

    private $content_object;
    

    function run()
    {
        $rdm = RepositoryDataManager :: get_instance();
        if($this->content_object = $rdm->retrieve_content_object(Request :: get(ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID)))
        {
            if (!RepoViewer::is_ready_to_be_published())
            {
                $repo_viewer = RepoViewer :: construct($this);
                $repo_viewer->set_parameter(ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID, $this->content_object->get_id());
                $repo_viewer->run();
            }
            else
            {
                $objects = RepoViewer::get_selected_objects();
                if (! is_array($objects))
                {
                    $objects = array($objects);
                }

                $rdm = RepositoryDataManager :: get_instance();
                $success = true;

                foreach ($objects as $object_id)
                {
                    $this->redirect(Translation :: get(ContenObjectSelected), false, array(ContextLinkerManager :: PARAM_ACTION => ContextLinkerManager :: ACTION_PUBLISH_CONTEXT_LINK, ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID => Request :: get(ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID), ContextLinkerManager :: PARAM_ALTERNATIVE_CONTENT_OBJECT_ID => $object_id));
                }
            }
        }
    }

    function get_allowed_content_object_types()
    {
        return array(Document :: get_type_name(), Youtube :: get_type_name(), $this->content_object->get_type());
    }


    
    

}
?>