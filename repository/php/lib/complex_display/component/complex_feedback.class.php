<?php
namespace repository;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\BreadcrumbTrail;
use common\libraries\Theme;
/**
 * $Id: complex_feedback.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component
 */
require_once Path :: get_application_path() . 'lib/weblcms/content_object_repo_viewer.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/feedback/feedback.class.php';

class ComplexDisplayComponentComplexFeedbackComponent extends ComplexDisplayComponent implements RepoViewerInterface
{
    private $pub;
    private $content_object;
    private $cid;
    private $fid;

    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('courses general');

        $this->pub = RepoViewer :: construct($this);
        $this->pub->set_maximum_select(RepoViewer :: SELECT_SINGLE);
        $this->pub->set_parameter(ComplexDisplay :: PARAM_DISPLAY_ACTION, WikiDisplay :: ACTION_FEEDBACK_CLOI);
        $this->pub->set_parameter(ComplexDisplay :: PARAM_ROOT_CONTENT_OBJECT, Request :: get(ComplexDisplay :: PARAM_ROOT_CONTENT_OBJECT));
        $this->pub->set_parameter('selected_cloi', Request :: get('selected_cloi'));

        switch (Request :: get('tool'))
        {
            case 'learning_path' :
                $tool_action = 'view_clo';
                break;
            default :
                $tool_action = 'view';
                break;
        }

        if (! $this->pub->is_ready_to_be_published())
        {
            $html[] = '<p><a href="' . $this->get_url() . '"><img src="' . Theme :: get_common_image_path() . 'action_browser.png" alt="' . Translation :: get('BrowserTitle') . '" style="vertical-align:middle;"/> ' . Translation :: get('BrowserTitle') . '</a></p>';
            $html[] = $this->pub->as_html();
            $this->display_header($trail, true);
            echo implode("\n", $html);
            $this->display_footer();
        }
        else
        {
            $feedback = new Feedback();
            $feedback->set_id($this->pub->get_selected_objects());
            $this->fid = $feedback->get_id();
            $this->cid = Request :: get('selected_cloi');
            $this->content_object = $this->get_root_content_object()->get_id();

            /*
             * change in the feedback, create new tabel linking the feedback object to the wiki_page
             */

            //$rdm = RepositoryDataManager :: get_instance();
            $content_object_pub_feedback = new ContentObjectPubFeedback();
            if (isset($this->cid))
                $content_object_pub_feedback->set_cloi_id($this->cid);
            else
                $content_object_pub_feedback->set_cloi_id(0);

            if (isset($this->content_object))
                $content_object_pub_feedback->set_publication_id($this->content_object);
            else
                $content_object_pub_feedback->set_publication_id(0);

            if (isset($this->fid))
                $content_object_pub_feedback->set_feedback_id($this->fid);
            else
                $content_object_pub_feedback->set_feedback_id(0);

            $content_object_pub_feedback->create();

            $this->redirect(Translation :: get('FeedbackAdded'), '', array(
                    Tool :: PARAM_ACTION => Request :: get('tool_action'), 'display_action' => 'discuss', 'selected_cloi' => $this->pub->get_parameter('selected_cloi'),
                    ComplexDisplay :: PARAM_ROOT_CONTENT_OBJECT => $this->content_object));
        }
    }

    function get_allowed_content_object_types()
    {
        return array(Feedback :: get_type_name());
    }
}
?>