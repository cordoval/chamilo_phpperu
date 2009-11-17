<?php
/**
 * $Id: complex_feedback.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component
 */
require_once Path :: get_application_path() . 'lib/weblcms/content_object_repo_viewer.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/feedback/feedback.class.php';

class ComplexDisplayComplexFeedbackComponent extends ComplexDisplayComponent
{
    private $pub;
    private $pid;
    private $cid;
    private $fid;

    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add_help('courses general');
        
        $object = Request :: get('object');
        $this->pub = new RepoViewer($this, 'feedback', true);
        $this->pub->set_parameter(ComplexDisplay :: PARAM_DISPLAY_ACTION, WikiDisplay :: ACTION_FEEDBACK_CLOI);
        $this->pub->set_parameter('pid', Request :: get('pid'));
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
        
        if (! isset($object))
        {
            $html[] = '<p><a href="' . $this->get_url() . '"><img src="' . Theme :: get_common_image_path() . 'action_browser.png" alt="' . Translation :: get('BrowserTitle') . '" style="vertical-align:middle;"/> ' . Translation :: get('BrowserTitle') . '</a></p>';
            $html[] = $this->pub->as_html();
            //$this->display_header($trail, true);
            echo implode("\n", $html);
            //$this->display_footer();
        }
        else
        {
            $feedback = new Feedback();
            $feedback->set_id($object);
            $this->fid = $feedback->get_id();
            $this->cid = Request :: get('selected_cloi');
            $this->pid = Request :: get('pid');
            
            /*
             * change in the feedback, create new tabel linking the feedback object to the wiki_page
             */
            
            //$rdm = RepositoryDataManager :: get_instance();
            $content_object_pub_feedback = new ContentObjectPubFeedback();
            if (isset($this->cid))
                $content_object_pub_feedback->set_cloi_id($this->cid);
            else
                $content_object_pub_feedback->set_cloi_id(0);
            
            if (isset($this->pid))
                $content_object_pub_feedback->set_publication_id($this->pid);
            else
                $content_object_pub_feedback->set_publication_id(0);
            
            if (isset($this->fid))
                $content_object_pub_feedback->set_feedback_id($this->fid);
            else
                $content_object_pub_feedback->set_feedback_id(0);
            
            $content_object_pub_feedback->create();
            
            $this->redirect(Translation :: get('FeedbackAdded'), '', array(Tool :: PARAM_ACTION => Request :: get('tool_action'), 'display_action' => 'discuss', 'selected_cloi' => $this->pub->get_parameter('selected_cloi'), 'pid' => $this->pid));
        }
    }
}
?>
