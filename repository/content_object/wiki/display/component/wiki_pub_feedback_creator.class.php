<?php
/**
 * $Id: wiki_pub_feedback_creator.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.wiki.component
 */
require_once Path :: get_application_path() . 'lib/weblcms/content_object_repo_viewer.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/feedback/feedback.class.php';
require_once Path :: get_application_path() . 'lib/wiki/wiki_pub_feedback.class.php';

class WikiDisplayWikiPubFeedbackCreatorComponent extends WikiDisplay implements RepoViewerInterface
{
    private $pub;
    private $wiki_publication_id;
    private $complex_id;
    private $feedback_id;

    function run()
    {
        

        

        if (!RepoViewer::is_ready_to_be_published())
        {
            $this->pub = RepoViewer :: construct($this);
            $this->pub->set_maximum_select(RepoViewer :: SELECT_SINGLE);
            //$this->pub->set_parameter(ComplexDisplay :: PARAM_DISPLAY_ACTION, WikiDisplay :: ACTION_D);
            $this->pub->set_parameter(WikiManager :: PARAM_WIKI_PUBLICATION, Request :: get(WikiManager :: PARAM_WIKI_PUBLICATION));
            $this->pub->set_parameter(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID, Request :: get(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID));
            $html[] = '<p><a href="' . $this->get_url() . '"><img src="' . Theme :: get_common_image_path() . 'action_browser.png" alt="' . Translation :: get('BrowserTitle') . '" style="vertical-align:middle;"/> ' . Translation :: get('BrowserTitle') . '</a></p>';
            $html[] = $this->pub->as_html();

            $this->display_header();
            echo implode("\n", $html);
            $this->display_footer();
        }
        else
        {
            $feedback = new Feedback();
            $feedback->set_id(RepoViewer::get_selected_objects());
            $this->feedback_id = $feedback->get_id();
            $this->complex_id = Request :: get(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);
            $this->wiki_publication_id = Request :: get(WikiManager :: PARAM_WIKI_PUBLICATION);

            /*
             * change in the feedback, create new tabel linking the feedback object to the wiki_page
             */

            //$rdm = RepositoryDataManager :: get_instance();
            $wiki_pub_feedback = new WikiPubFeedback();
            if (isset($this->complex_id))
                $wiki_pub_feedback->set_cloi_id($this->complex_id);
            else
                $wiki_pub_feedback->set_cloi_id(0);

            if (isset($this->wiki_publication_id))
                $wiki_pub_feedback->set_wiki_publication_id($this->wiki_publication_id);
            else
                $wiki_pub_feedback->set_wiki_publication_id(0);

            if (isset($this->feedback_id))
                $wiki_pub_feedback->set_feedback_id($this->feedback_id);
            else
                $wiki_pub_feedback->set_feedback_id(0);

            $wiki_pub_feedback->create();

            $this->redirect(Translation :: get('FeedbackAdded'), '', array(
                    Tool :: PARAM_ACTION => Request :: get('tool_action'), 'display_action' => 'discuss',
                    ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->pub->get_parameter(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID),
                    'wiki_publication' => $this->wiki_publication_id));
        }
    }

    function get_allowed_content_object_types()
    {
        return array(Feedback :: get_type_name());
    }
    
    function  add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail = $this->get_breadcrumbtrail();
        $breadcrumbtrail->add_help('courses general');
    }
}
?>