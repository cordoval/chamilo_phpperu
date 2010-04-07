<?php
/**
 * $Id: complex_feedback.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.component
 */
require_once dirname(__FILE__) . '/../tool.class.php';
require_once dirname(__FILE__) . '/../tool_component.class.php';
require_once dirname(__FILE__) . '/../../content_object_repo_viewer.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/feedback/feedback.class.php';

class ToolComplexFeedbackComponent extends ToolComponent
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
        $this->pub = new ContentObjectRepoViewer($this, 'feedback', true);
        $this->pub->set_parameter(Tool :: PARAM_ACTION, Tool :: ACTION_FEEDBACK_CLOI);
        
        switch (Request :: get('tool'))
        {
            case 'learning_path' :
                $tool_action = 'view_clo';
                break;
            default :
                $tool_action = 'view';
                break;
        }
        
        if (Request :: get(Tool :: PARAM_PUBLICATION_ID))
        {
            $this->pub->set_parameter(Tool :: PARAM_PUBLICATION_ID, Request :: get(Tool :: PARAM_PUBLICATION_ID));
            $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => $tool_action, 'display_action' => 'view', Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID))), WebLcmsDataManager :: get_instance()->retrieve_content_object_publication(Request :: get(Tool :: PARAM_PUBLICATION_ID))->get_content_object()->get_title()));
        }
        
        if (Request :: get('cid'))
        {
            $this->pub->set_parameter('cid', Request :: get('cid'));
            $cloi = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item(Request :: get('cid'));
            $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => $tool_action, 'display_action' => 'view_item', Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID), Tool :: PARAM_COMPLEX_ID => $cloi->get_id())), RepositoryDataManager :: get_instance()->retrieve_content_object($cloi->get_ref())->get_title()));
        }
        
        if (Request :: get('tool') == 'wiki' || Request :: get('tool') == 'learning_path')
            $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => $tool_action, 'display_action' => 'discuss', Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID), Tool :: PARAM_COMPLEX_ID => Request :: get('cid'))), Translation :: get('Discuss')));
        $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_FEEDBACK_CLOI, Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID), 'cid' => Request :: get('cid'))), Translation :: get('AddFeedback')));
        
        if (! isset($object))
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
            $feedback->set_id($object);
            $this->fid = $feedback->get_id();
            $this->cid = Request :: get('cid');
            $this->pid = Request :: get(Tool :: PARAM_PUBLICATION_ID);
            
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
            if (Request :: get('tool') == 'wiki' || Request :: get('tool') == 'learning_path')
                $this->redirect(Translation :: get('FeedbackAdded'), '', array(Tool :: PARAM_ACTION => Request :: get('tool') == 'learning_path' ? 'view_clo' : 'view', 'display_action' => Request :: get('cid') != null ? 'discuss' : 'view_item', Request :: get('cid') != null ? 'cid' : Tool :: PARAM_PUBLICATION_ID => $this->cid, Tool :: PARAM_PUBLICATION_ID => $this->pid));
            else
                $this->redirect(Translation :: get('FeedbackAdded'), '', array(Tool :: PARAM_ACTION => Request :: get('cid') != null ? 'discuss' : 'view_item', Request :: get('cid') != null ? 'cid' : Tool :: PARAM_PUBLICATION_ID => $this->cid, Tool :: PARAM_PUBLICATION_ID => $this->pid));
        
        }
    }
}
?>
