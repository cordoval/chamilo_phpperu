<?php
namespace application\context_linker;

use common\extensions\repo_viewer\RepoViewer;
use common\libraries\Request;
use common\libraries\Translation;
use repository\RepositoryDataManager;
use repository\content_object\document\Document;
use repository\content_object\youtube\Youtube;
use common\libraries\Utilities;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;

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
        $trail = new BreadcrumbTrail;
        $trail->add(new Breadcrumb($this->get_url(array(ContextLinkerManager :: PARAM_ACTION => null)), Translation :: get('ContextLinker')));
        $trail->add(new Breadcrumb(Translation :: get('CreateObject', array('OBJECT' => Translation::get('ContextLink')), Utilities::COMMON_LIBRARIES)));
        $trail->add_help('ContextLinkCreator');

        $redirect_url = Request :: get(ContextLinkerManager::PARAM_REDIRECT_URL);
        
        $rdm = RepositoryDataManager :: get_instance();
        if($this->content_object = $rdm->retrieve_content_object(Request :: get(ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID)))
        {
            if (!RepoViewer::is_ready_to_be_published())
            {
                $repo_viewer = RepoViewer :: construct($this);

                $cdm = ContextLinkerDataManager :: get_instance();
                $result = $cdm->retrieve_full_context_links_recursive($this->content_object->get_id(), null, null, null,  parent :: ARRAY_TYPE_FLAT);

                foreach($result as $n => $v)
                {
                    $result0[] = $v[ContextLinkerManager :: PROPERTY_ALT_ID];
                }

                unset($result);

                array_push($result0, $this->content_object->get_id());
                
                $repo_viewer->set_excluded_objects($result0);
                $repo_viewer->set_parameter(ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID, $this->content_object->get_id());
                $repo_viewer->set_parameter(ContextLinkerManager::PARAM_REDIRECT_URL, $redirect_url);

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
                    $this->redirect(Translation :: get('ObjectSelected', array('OBJECT' => Translation :: get('AlternativeContentObject')), Utilities :: COMMON_LIBRARIES), false, array(ContextLinkerManager :: PARAM_ACTION => ContextLinkerManager :: ACTION_PUBLISH_CONTEXT_LINK, ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID => Request :: get(ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID), ContextLinkerManager :: PARAM_ALTERNATIVE_CONTENT_OBJECT_ID => $object_id, ContextLinkerManager::PARAM_REDIRECT_URL => $redirect_url));
                }
            }
        }
        else
        {
            $this->display_header($trail);
            echo '<p>' . Translation :: get('NoContentObjectSelected', null, 'repository') . '</p>';
            $this->display_footer();
        }
    }

    

    function get_allowed_content_object_types()
    {
        return array(Document :: get_type_name(), Youtube :: get_type_name(), $this->content_object->get_type());
    }
}
?>