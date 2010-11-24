<?php
//namespace application\weblcms\tool\link;
//
//use application\weblcms\WeblcmsRights;
//use application\weblcms\Tool;
//use common\extensions\repo_viewer\RepoViewerInterface;
//use common\extensions\repo_viewer\RepoViewer;
//use common\libraries\Display;
//use common\libraries\Breadcrumb;
//use common\libraries\BreadcrumbTrail;
//use common\libraries\Request;
//use common\libraries\Translation;
//use application\weblcms\ContentObjectPublisher;
//
///**
// * $Id: link_publisher.class.php 216 2009-11-13 14:08:06Z kariboe $
// * @package application.lib.weblcms.tool.link.component
// */
//
//class LinkToolPublisherComponent extends LinkTool implements RepoViewerInterface
//{
//
//    function run()
//    {
//        if (! $this->is_allowed(WeblcmsRights :: ADD_RIGHT))
//        {
//            Display :: not_allowed();
//            return;
//        }
//
//        $trail = BreadcrumbTrail :: get_instance();
//
//        if (Request :: get('pcattree') != null)
//        {
//            foreach (Tool :: get_pcattree_parents(Request :: get('pcattree')) as $breadcrumb)
//            {
//                if ($breadcrumb)
//                    $trail->add(new Breadcrumb($this->get_url(), $breadcrumb->get_name()));
//            }
//        }
//
//        if (! ContentObjectRepoViewer :: is_ready_to_be_published())
//        {
//            $pub = new ContentObjectRepoViewer($this);
//            $html[] = $pub->as_html();
//        }
//        else
//        {
//            $publisher = new ContentObjectPublisher($pub);
//            $html[] = $publisher->get_publications_form($pub->get_selected_objects());
//        }
//
//        $this->display_header();
//        echo implode("\n", $html);
//        $this->display_footer();
//    }
//
//    public function get_allowed_content_object_types()
//    {
//        return array(Link :: get_type_name());
//    }
//
//    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
//    {
//        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('LinkToolBrowserComponent')));
//        $trail->add_help('weblcms_link_publisher');
//    }
//
//    function get_additional_parameters()
//    {
//        return array(RepoViewer :: PARAM_ID, RepoViewer :: PARAM_ACTION);
//    }
//}
?>