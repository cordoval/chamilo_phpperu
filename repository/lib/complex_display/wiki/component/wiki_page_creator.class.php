<?php
/**
 * $Id: wiki_page_creator.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package repository.lib.complex_display.wiki.component
 */
/*
 * This is the compenent that allows the user to create a wiki_page.
 *
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

require_once Path :: get_application_path() . 'lib/weblcms/content_object_repo_viewer.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/publisher/content_object_publisher.class.php';
require_once Path :: get_repository_path() . 'lib/complex_builder/complex_repo_viewer.class.php';

class WikiDisplayWikiPageCreatorComponent extends WikiDisplayComponent
{
    private $pub;

    function run()
    {
        $this->pub = new RepoViewer($this, 'wiki_page', false, RepoViewer :: SELECT_SINGLE);
        $this->pub->set_parameter(ComplexDisplay :: PARAM_DISPLAY_ACTION, WikiDisplay :: ACTION_CREATE_PAGE);
        $this->pub->set_parameter('pid', $this->get_parent()->get_root_lo()->get_id());

        if ($this->pub->is_ready_to_be_published())
        {
            $html[] = '<div id="trailbox2" style="padding:0px;">' . $this->get_parent()->get_breadcrumbtrail()->render() . '<br /><br /><br /></div>';
            $html[] = $this->pub->as_html();
           
            $this->display_header(new BreadcrumbTrail());
            echo implode("\n", $html);
            $this->display_footer();

        }
        else
        {
            $o = RepositoryDataManager :: get_instance()->retrieve_content_object($this->pub->get_selected_objects());
            $count = RepositoryDataManager :: get_instance()->count_type_content_objects('wiki_page', new EqualityCondition(ContentObject :: PROPERTY_TITLE, $o->get_title()));
            if ($count == 1)
            {
                $cloi = ComplexContentObjectItem :: factory('wiki_page');
                $cloi->set_ref($this->pub->get_selected_objects());
                $cloi->set_parent($this->get_root_lo()->get_id());
                $cloi->set_user_id($this->pub->get_user_id());
                $cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($this->get_root_lo()->get_id()));
                $cloi->set_additional_properties(array('is_homepage' => 0));
                $cloi->create();
                $this->redirect(Translation :: get('WikiItemCreated'), '', array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI_PAGE, 'selected_cloi' => $cloi->get_id(), 'pid' => $this->get_root_lo()->get_id()));
            }
            else
            {
                $this->display_header(new BreadcrumbTrail());
                $this->display_error_message(Translation :: get('WikiPageTitleError'));
                echo $this->pub->as_html();
                $this->display_footer();
            }
        }

    }
}
?>