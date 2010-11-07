<?php
namespace repository\content_object\forum_topic;

use common\libraries\Translation;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\extensions\repo_viewer\RepoViewerInterface;
use repository\RepositoryDataManager;
use repository\content_object\forum_post\ForumPost;
use common\extensions\repo_viewer\RepoViewer;
use repository\ComplexDisplay;
use repository\ContentObject;
use repository\ComplexContentObjectItem;

class ForumTopicDisplayCreatorComponent extends ForumTopicDisplay implements
        RepoViewerInterface
{

    function run()
    {
        $selected_complex_content_object_item = $this->get_selected_complex_content_object_item();
        $rdm = RepositoryDataManager :: get_instance();

        if ($selected_complex_content_object_item)
        {
            $reply_lo = $rdm->retrieve_content_object($selected_complex_content_object_item->get_ref(), ForumPost :: get_type_name());
        }

        if (! RepoViewer :: is_ready_to_be_published())
        {
            $repo_viewer = RepoViewer :: construct($this);
            $repo_viewer->set_parameter(ComplexDisplay :: PARAM_DISPLAY_ACTION, ForumTopicDisplay :: ACTION_CREATE_FORUM_POST);
            $repo_viewer->set_parameter(ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID, $this->get_complex_content_object_item_id());

            if ($reply_lo)
            {
                if (substr($reply_lo->get_title(), 0, 3) == 'RE:')
                {
                    $reply = $reply_lo->get_title();
                }
                else
                {
                    $reply = 'RE: ' . $reply_lo->get_title();
                }

                $repo_viewer->set_creation_defaults(array(
                        ContentObject :: PROPERTY_TITLE => $reply));
            }

            $trail = BreadcrumbTrail :: get_instance();
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('reply_on_post')));

            $repo_viewer->run();
        }
        else
        {
            $object_ids = RepoViewer :: get_selected_objects();

            if (! is_array($object_ids))
            {
                $object_ids = array(
                        $object_ids);
            }

            foreach ($object_ids as $object_id)
            {
                $cloi = ComplexContentObjectItem :: factory(ForumPost :: get_type_name());

                $cloi->set_ref($object_id);
                $cloi->set_user_id($this->get_user_id());
                $cloi->set_parent($this->get_root_content_object()->get_id());
                $cloi->set_display_order($rdm->select_next_display_order($cloi->get_parent()));

                if ($selected_complex_content_object_item)
                {
                    $cloi->set_reply_on_post($selected_complex_content_object_item->get_id());
                }

                $cloi->create();
            }

            $this->my_redirect();
        }
    }

    private function my_redirect()
    {
        $message = htmlentities(Translation :: get('ContentObjectCreated'));

        $params = array();
        $params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT;
        $params[ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();

        $this->redirect($message, '', $params);
    }

    function get_allowed_content_object_types()
    {
        return array(
                ForumPost :: get_type_name());
    }

}
?>