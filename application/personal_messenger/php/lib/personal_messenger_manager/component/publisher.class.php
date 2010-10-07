<?php
/**
 * $Id: publisher.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.personal_messenger.personal_messenger_manager.component
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once WebApplication :: get_application_class_lib_path('personal_messenger') . 'personal_messenger_manager/personal_messenger_manager.class.php';
require_once WebApplication :: get_application_class_lib_path('personal_messenger') . 'publisher/personal_message_publisher.class.php';

class PersonalMessengerManagerPublisherComponent extends PersonalMessengerManager implements RepoViewerInterface
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $reply = Request :: get('reply');
        $user = Request :: get(PersonalMessengerManager :: PARAM_USER_ID);

        if (!RepoViewer::is_ready_to_be_published())
        {
            $repo_viewer = RepoViewer :: construct($this);
            $repo_viewer->set_maximum_select(RepoViewer :: SELECT_SINGLE);
            $repo_viewer->set_parameter('reply', $reply);
            $repo_viewer->set_parameter(PersonalMessengerManager :: PARAM_USER_ID, $user);

            if ($reply)
            {
                $publication = PersonalMessengerDataManager :: get_instance()->retrieve_personal_message_publication($reply);
                $lo_id = $publication->get_personal_message();
                $lo = RepositoryDataManager :: get_instance()->retrieve_content_object($lo_id, PersonalMessage :: get_type_name());
                $title = $lo->get_title();
                $defaults['title'] = (substr($title, 0, 3) == 'RE:') ? $title : 'RE: ' . $title;
                $repo_viewer->set_creation_defaults($defaults);
            }
            $repo_viewer->run();
        }
        else
        {
            $publisher = new PersonalMessagePublisher($this);
            $publisher->get_publication_form(RepoViewer::get_selected_objects());
        }
    }

    function get_allowed_content_object_types()
    {
        return array(PersonalMessage :: get_type_name());
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_VIEW_PUBLICATION, self :: PARAM_PERSONAL_MESSAGE_ID => Request :: get(self :: PARAM_PERSONAL_MESSAGE_ID))), Translation :: get('PersonalMessengerManagerViewerComponent')));
    	$breadcrumbtrail->add_help('personal_messenger_publisher');
    }
    
    function get_additional_parameters()
    {
    	return array(self :: PARAM_USER_ID, 'reply');
    }
}
?>