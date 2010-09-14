<?php
/**
 * $Id: personal_message_publisher.class.php 203 2009-11-13 12:46:38Z chellee $
 * @package application.personal_messenger.publisher
 */
require_once dirname(__FILE__) . '/../personal_message_publication_form.class.php';
/**
 * This class represents a profile publisher component which can be used
 * to create a new learning object before publishing it.
 */
class PersonalMessagePublisher
{
    /**
     * Gets the form to publish the learning object.
     * @return string|null A HTML-representation of the form. When the
     * publication form was validated, this function will send header
     * information to redirect the end user to the location where the
     * publication was made.
     */

    private $parent;

    function PersonalMessagePublisher($parent)
    {
        $this->parent = $parent;
    }

    function get_publication_form($content_object_id, $new = false)
    {
        $html = array();
        $html[] = ($new ? Display :: normal_message(htmlentities(Translation :: get('ContentObjectCreated')), true) : '');
        //$tool = $this->get_parent()->get_parent();
        $content_object = RepositoryDataManager :: get_instance()->retrieve_content_object($content_object_id);
        $edit = Request :: get('reply');
        $user = Request :: get(PersonalMessengerManager :: PARAM_USER_ID);

        $form_action_parameters = array_merge($this->parent->get_parameters(), array(RepoViewer :: PARAM_ID => $content_object->get_id(), RepoViewer :: PARAM_ACTION => RepoViewer :: ACTION_PUBLISHER));
        $form = new PersonalMessagePublicationForm($content_object, $this->parent->get_user(), $this->parent->get_url($form_action_parameters));
        if ($form->validate() || ($edit && (isset($user) && ! empty($user))))
        {
            $failures = 0;

            if ($edit)
            {
                $recipients = array();
                $recipients[] = $user;
            }

            if ($form->create_content_object_publication($recipients))
            {
                $message = 'PersonalMessagePublished';
            }
            else
            {
                $failures ++;
                $message = 'PersonalMessageNotPublished';
            }
            $this->parent->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => PersonalMessengerManager :: ACTION_BROWSE_MESSAGES));
        }
        else
        {
            $html[] = ContentObjectDisplay :: factory($content_object)->get_full_html();
            $html[] = $form->toHtml();
            $html[] = '<div style="clear: both;"></div>';

            $this->parent->display_header();
            echo implode("\n", $html);
            $this->parent->display_footer();
        }
    }
}
?>