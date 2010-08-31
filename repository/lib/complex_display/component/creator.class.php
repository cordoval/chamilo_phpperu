<?php

/**
 * $Id: creator.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component
 */

/**
 * @author Sven Vanpoucke
 */
class ComplexDisplayComponentCreatorComponent extends ComplexDisplayComponent implements RepoViewerInterface
{

    function run()
    {
        if ($this->is_allowed(ADD_RIGHT))
        {
            $complex_content_object_item_id = $this->get_complex_content_object_item_id();
            $complex_content_object_item = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($complex_content_object_item_id);

            if (!$this->get_root_content_object())
            {
                $this->display_header();
                $this->display_error_message(Translation :: get('NoParentSelected'));
                $this->display_footer();
                exit();
            }

            $type = Request :: get('type');

            if (!RepoViewer::is_ready_to_be_published())
            {
                $repo_viewer = RepoViewer :: construct($this);
                $repo_viewer->set_maximum_select(RepoViewer :: SELECT_SINGLE);
                $repo_viewer->set_parameter(ComplexDisplay :: PARAM_DISPLAY_ACTION, ComplexDisplay :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM);
                $repo_viewer->set_parameter('cid', $complex_content_object_item_id);
                $repo_viewer->set_parameter('type', $type);
                $repo_viewer->run();
            }
            else
            {
                $cloi = ComplexContentObjectItem :: factory($type);

                $cloi->set_ref(RepoViewer::get_selected_objects());
                $cloi->set_user_id($this->get_user_id());

                if ($complex_content_object_item_id)
                {
                    $cloi->set_parent($complex_content_object_item->get_ref());
                }
                else
                {
                    $cloi->set_parent($this->get_root_content_object()->get_id());
                }

                $cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($cloi->get_ref()));

                /* $cloi_form = ComplexContentObjectItemForm :: factory(ComplexContentObjectItemForm :: TYPE_CREATE, $cloi, 'create_complex', 'post', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_CREATE_CLOI, 'object' => $pub->get_selected_objects())));

                  if ($cloi_form)
                  {
                  if ($cloi_form->validate() || ! $cloi->is_extended())
                  {
                  $cloi_form->create_complex_content_object_item();
                  $this->my_redirect($complex_content_object_item_id);
                  }
                  else
                  {
                  $this->display_header(BreadcrumbTrail :: get_instance());
                  $cloi_form->display();
                  $this->display_footer();
                  }
                  }
                  else
                  { */
                $cloi->create();
                $this->my_redirect($complex_content_object_item_id);
                //}
            }
        }
    }

    private function my_redirect($complex_content_object_item_id)
    {
        $message = htmlentities(Translation :: get('ContentObjectCreated'));

        $params = array();
        $params['cid'] = $complex_content_object_item_id;
        $params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ComplexDisplay :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT;

        $this->redirect($message, '', $params);
    }

    function get_allowed_content_object_types()
    {
        return array(Request :: get('type'));
    }

}

?>