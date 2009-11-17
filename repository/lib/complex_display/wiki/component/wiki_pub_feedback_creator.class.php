<?php
/**
 * $Id: wiki_pub_feedback_creator.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.wiki.component
 */
require_once Path :: get_application_path() . 'lib/weblcms/content_object_repo_viewer.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/feedback/feedback.class.php';
require_once Path :: get_application_path() . 'lib/wiki/wiki_pub_feedback.class.php';

class WikiDisplayWikiPubFeedbackCreatorComponent extends WikiDisplayComponent
{
    private $pub;
    private $wiki_publication_id;
    private $cid;
    private $fid;

    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add_help('courses general');
        
        $object = Request :: get('object');
        $this->pub = new RepoViewer($this, 'feedback', true);
        $this->pub->set_parameter(ComplexDisplay :: PARAM_DISPLAY_ACTION, WikiDisplay :: ACTION_FEEDBACK_CLOI);
        $this->pub->set_parameter(WikiManager :: PARAM_WIKI_PUBLICATION, Request :: get(WikiManager :: PARAM_WIKI_PUBLICATION));
        $this->pub->set_parameter('selected_cloi', Request :: get('selected_cloi'));
        
        if (! isset($object))
        {
            $html[] = '<p><a href="' . $this->get_url() . '"><img src="' . Theme :: get_common_image_path() . 'action_browser.png" alt="' . Translation :: get('BrowserTitle') . '" style="vertical-align:middle;"/> ' . Translation :: get('BrowserTitle') . '</a></p>';
            $html[] = $this->pub->as_html();
            echo implode("\n", $html);
        }
        else
        {
            $feedback = new Feedback();
            $feedback->set_id($object);
            $this->fid = $feedback->get_id();
            $this->cid = Request :: get('selected_cloi');
            $this->wiki_publication_id = Request :: get(WikiManager :: PARAM_WIKI_PUBLICATION);
            
            /*
             * change in the feedback, create new tabel linking the feedback object to the wiki_page
             */
            
            //$rdm = RepositoryDataManager :: get_instance();
            $wiki_pub_feedback = new WikiPubFeedback();
            if (isset($this->cid))
                $wiki_pub_feedback->set_cloi_id($this->cid);
            else
                $wiki_pub_feedback->set_cloi_id(0);
            
            if (isset($this->wiki_publication_id))
                $wiki_pub_feedback->set_wiki_publication_id($this->wiki_publication_id);
            else
                $wiki_pub_feedback->set_wiki_publication_id(0);
            
            if (isset($this->fid))
                $wiki_pub_feedback->set_feedback_id($this->fid);
            else
                $wiki_pub_feedback->set_feedback_id(0);
            
            $wiki_pub_feedback->create();
            
            $this->redirect(Translation :: get('FeedbackAdded'), '', array(Tool :: PARAM_ACTION => Request :: get('tool_action'), 'display_action' => 'discuss', 'selected_cloi' => $this->pub->get_parameter('selected_cloi'), 'wiki_publication' => $this->wiki_publication_id));
        }
    }
}
?>
