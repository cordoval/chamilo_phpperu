<?php
/**
 * $Id: introducer.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.lib.alexia.alexia_manager.component
 */
require_once dirname(__FILE__) . '/../alexia_manager.class.php';
require_once dirname(__FILE__) . '/../../forms/alexia_publication_form.class.php';

class AlexiaManagerIntroducerComponent extends AlexiaManager
{

    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => AlexiaManager :: ACTION_BROWSE_PUBLICATIONS)), Translation :: get('Alexia')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('PublishIntroductionText')));
        $trail->add_help('alexia general');

        $repo_viewer = new RepoViewer($this, Introduction :: get_type_name());
        $repo_viewer->set_parameter(AlexiaManager :: PARAM_ACTION, AlexiaManager :: ACTION_PUBLISH_INTRODUCTION);

        if (!$repo_viewer->is_ready_to_be_published())
        {
            //$html = array();
            //$html[] = '<p><a href="' . $this->get_url() . '"><img src="' . Theme :: get_common_image_path() . 'action_browser.png" alt="' . Translation :: get('BrowserTitle') . '" style="vertical-align:middle;"/> ' . Translation :: get('BrowserTitle') . '</a></p>';
            $repo_viewer->run();
        }
        else
        {
            $publication = new AlexiaPublication();
            $publication->set_content_object($repo_viewer->get_selected_objects());
            $publication->set_target_users(array());
            $publication->set_target_groups(array());
            $publication->set_from_date(0);
            $publication->set_to_date(0);
            $publication->set_publisher(Session :: get_user_id());
            $publication->set_published(time());
            $publication->set_hidden(0);

            if ($publication->create())
            {
                $this->redirect(Translation :: get('IntroductionPublished'), false, array(Application :: PARAM_ACTION => AlexiaManager :: ACTION_BROWSE_PUBLICATIONS));
            }
            else
            {
                $this->display_header($trail, true);
                $this->display_error_message(Translation :: get('IntroductionNotPublished'));
                $this->display_footer();
            }
        }
    }
}
?>