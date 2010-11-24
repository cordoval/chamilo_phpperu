<?php
namespace common\extensions\feedback_manager;

use common\extensions\repo_viewer\RepoViewerInterface;
use admin\FeedbackPublication;
use repository\content_object\feedback\Feedback;
use common\libraries\Translation;
/**
 * $Id: creator.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.common.feedback_manager.component
 */

/**
 * Description of updaterclass
 *
 * @author pieter
 */

class FeedbackManagerCreatorComponent extends FeedbackManager implements RepoViewerInterface
{

    function run()
    {
        $application = $this->get_application();
        $publication_id = $this->get_publication_id();
        $complex_wrapper_id = $this->get_complex_wrapper_id();
        $action = $this->get_action();


        if (!RepoViewer :: is_ready_to_be_published())
        {
            $repo_viewer = RepoViewer :: construct($this);
            $repo_viewer->run();
        }
        else
        {
            $objects = RepoViewer :: get_selected_objects();

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
            $redirect = $this->redirect(Translation :: get($message), false, array(FeedbackManager :: PARAM_ACTION => $this->get_parameter(self :: PARAM_OLD_ACTION), RepoViewer :: PARAM_ACTION => null));
        }

    }

    function get_allowed_content_object_types()
    {
        return array(Feedback :: get_type_name());
    }
}
?>