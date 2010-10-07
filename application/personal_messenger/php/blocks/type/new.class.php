<?php
/**
 * $Id: new.class.php 203 2009-11-13 12:46:38Z chellee $
 * @package application.personal_messenger.block
 */
require_once WebApplication :: get_application_class_path('personal_messenger') . 'blocks/personal_messenger_block.class.php';
require_once Path :: get_library_path() . 'utilities.class.php';
/**
 * This class represents a calendar publisher component which can be used
 * to browse through the possible learning objects to publish.
 */
class PersonalMessengerNew extends PersonalMessengerBlock
{

    function run()
    {
        return $this->as_html();
    }

    /*
	 * Inherited
	 */
    function as_html()
    {
        $html = array();

        $personal_messenger = $this->get_parent();

        $html[] = $this->display_header();

        $publications = $personal_messenger->retrieve_personal_message_publications($this->get_condition(), array(), array(), 5);

        if ($publications->size() > 0)
        {
            $html[] = '<ul>';
            while ($publication = $publications->next_result())
            {
                $html[] = '<li>';
                $html[] = '<a href="' . $personal_messenger->get_publication_viewing_link($publication) . '">' . $publication->get_publication_object()->get_title() . '</a>';
                $html[] = '</li>';
            }
            $html[] = '</ul>';
        }
        else
        {
            $html[] = Translation :: get('NoNewMessages');
        }

        $html[] = $this->display_footer();

        return implode("\n", $html);
    }

    function get_condition()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(PersonalMessagePublication :: PROPERTY_STATUS, '1');
        $conditions[] = new EqualityCondition(PersonalMessagePublication :: PROPERTY_RECIPIENT, $this->get_user_id());
        $conditions[] = new EqualityCondition(PersonalMessagePublication :: PROPERTY_USER, $this->get_user_id());
        return new AndCondition($conditions);
    }
}
?>