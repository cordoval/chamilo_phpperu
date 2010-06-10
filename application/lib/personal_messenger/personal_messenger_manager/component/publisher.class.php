<?php
/**
 * $Id: publisher.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.personal_messenger.personal_messenger_manager.component
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__) . '/../personal_messenger_manager.class.php';
require_once dirname(__FILE__) . '/../../publisher/personal_message_publisher.class.php';

class PersonalMessengerManagerPublisherComponent extends PersonalMessengerManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $reply = Request :: get('reply');
        $user = Request :: get(PersonalMessengerManager :: PARAM_USER_ID);
        
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('personal messenger general');
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => PersonalMessengerManager :: ACTION_BROWSE_MESSAGES, PersonalMessengerManager :: PARAM_FOLDER => PersonalMessengerManager :: ACTION_FOLDER_INBOX)), Translation :: get('MyPersonalMessenger')));
        
        $pub = new RepoViewer($this, PersonalMessage :: get_type_name(), RepoViewer :: SELECT_SINGLE);
        $pub->set_parameter('reply', $reply);
        $pub->set_parameter(PersonalMessengerManager :: PARAM_USER_ID, $user);
        
        if (!$pub->is_ready_to_be_published())
        {
            if ($reply)
            {
                $publication = PersonalMessengerDataManager :: get_instance()->retrieve_personal_message_publication($reply);
                $lo_id = $publication->get_personal_message();
                $lo = RepositoryDataManager :: get_instance()->retrieve_content_object($lo_id, PersonalMessage :: get_type_name());
                $title = $lo->get_title();
                $defaults['title'] = (substr($title, 0, 3) == 'RE:') ? $title : 'RE: ' . $title;
                $pub->set_creation_defaults($defaults);
                
                $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => PersonalMessengerManager :: ACTION_BROWSE_MESSAGES, PersonalMessengerManager :: PARAM_FOLDER => PersonalMessengerManager :: ACTION_FOLDER_INBOX)), Translation :: get(ucfirst(PersonalMessengerManager :: ACTION_FOLDER_INBOX))));
                $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => PersonalMessengerManager :: ACTION_VIEW_PUBLICATION, PersonalMessengerManager :: PARAM_PERSONAL_MESSAGE_ID => $reply, PersonalMessengerManager :: PARAM_FOLDER => PersonalMessengerManager :: ACTION_FOLDER_INBOX)), $lo->get_title()));
                $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Reply')));
            }
            else
            {
                $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Send')));
            }
            $html[] = $pub->as_html();
        }
        else
        {
            //$html[] = 'ContentObject: ';
            $publisher = new PersonalMessagePublisher($pub);
            $html[] = $publisher->get_publication_form($pub->get_selected_objects());
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Send')));
        }
        
        $this->display_header($trail);
        echo implode("\n", $html);
        echo '<div style="clear: both;"></div>';
        $this->display_footer();
    }
}
?>