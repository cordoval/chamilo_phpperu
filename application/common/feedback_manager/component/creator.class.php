<?php
/**
 * $Id: creator.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.common.feedback_manager.component
 */

/**
 * Description of updaterclass
 *
 * @author pieter
 */

class FeedbackManagerCreatorComponent extends FeedbackManager
{

    function run()
    {
        $application = $this->get_application();
        $publication_id = $this->get_publication_id();
        $complex_wrapper_id = $this->get_complex_wrapper_id();
        $action = $this->get_action();
        $repo_viewer = new RepoViewer($this, Feedback :: get_type_name());
        if ($action == self :: ACTION_CREATE_ONLY_FEEDBACK)
        {
            $success = $repo_viewer->set_repo_viewer_actions(RepoViewer :: ACTION_CREATOR);
        }

        if (! $repo_viewer->is_ready_to_be_published())
        {
            $pub->run();
        }
        else
        {
            $objects = $repo_viewer->get_selected_objects();

            if (! is_array($objects))
            {
                $objects = array($objects);
            }

            foreach ($objects as $object)
            {
                $fb = new FeedbackPublication();
                $fb->set_application($application);
                $fb->set_cid($complex_wrapper_id);
                $fid = $object;
                $fb->set_fid($object);
                $fb->set_pid($publication_id);
                $fb->set_creation_date(time());
                $fb->set_modification_date(time());
                $fb->create();
            }

            $message = 'FeedbackCreated';
            if ($action == self :: ACTION_CREATE_ONLY_FEEDBACK)
            {
                $redirect = $this->redirect(Translation :: get($message), false, array(FeedbackManager :: PARAM_ACTION => FeedbackManager :: ACTION_CREATE_ONLY_FEEDBACK));
            }
            else
            {
                $this->redirect(Translation :: get($message), false, array(FeedbackManager :: PARAM_ACTION => FeedbackManager :: ACTION_BROWSE_FEEDBACK));
            }
        }
    }
}
?>