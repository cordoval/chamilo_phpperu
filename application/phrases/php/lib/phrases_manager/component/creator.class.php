<?php
namespace application\phrases;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;

use common\extensions\repo_viewer\RepoViewer;
use common\extensions\repo_viewer\RepoViewerInterface;

use repository\content_object\adaptive_assessment\AdaptiveAssessment;
use repository\content_object\hotpotatoes\Hotpotatoes;
/**
 * $Id: creator.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.lib.phrases.phrases_manager.component
 */
require_once dirname(__FILE__) . '/../phrases_manager.class.php';
require_once dirname(__FILE__) . '/../../publisher/phrases_publisher.class.php';

/**
 * Component to create a new phrases_publication object
 * @author Hans De Bisschop
 * @author
 */
class PhrasesManagerCreatorComponent extends PhrasesManager implements RepoViewerInterface
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        if (! RepoViewer :: is_ready_to_be_published())
        {
            $repo_viewer = RepoViewer :: construct($this);
            $repo_viewer->run();
        }
        else
        {
            $publisher = new PhrasesPublisher($this);
            $publisher->get_publications_form(RepoViewer :: get_selected_objects());
        }
    }

    function get_allowed_content_object_types()
    {
        return array(AdaptiveAssessment :: get_type_name());
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('phrases_creator');
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                PhrasesManager :: PARAM_ACTION => PhrasesManager :: ACTION_BROWSE_PHRASES_PUBLICATIONS)), Translation :: get('PhrasesManagerBrowserComponent')));
    }

}
?>