<?php

namespace application\personal_messenger;

use common\libraries\WebApplication;
use common\libraries\Translation;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
/**
 * $Id: new.class.php 203 2009-11-13 12:46:38Z chellee $
 * @package application.personal_messenger.block
 */
require_once WebApplication :: get_application_class_path('personal_messenger') . 'blocks/personal_messenger_block.class.php';
/**
 * This class represents a calendar publisher component which can be used
 * to browse through the possible learning objects to publish.
 */
class PersonalMessengerNew extends PersonalMessengerBlock
{

    /*
	 * Inherited
	 */
    function as_html()
    {
        $html = array();

        $html[] = $this->display_header();

        $publications = PersonalMessengerDataManager :: get_instance()->retrieve_personal_message_publications($this->get_condition(), array(), array(), 5);

        if ($publications->size() > 0)
        {
            $html[] = '<ul>';
            while ($publication = $publications->next_result())
            {
                $html[] = '<li>';
                $html[] = '<a href="' . $this->get_publication_viewing_link($publication) . '">' . $publication->get_publication_object()->get_title() . '</a>';
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
        $conditions[] = new EqualityCondition(PersonalMessengerPublication :: PROPERTY_STATUS, '1');
        $conditions[] = new EqualityCondition(PersonalMessengerPublication :: PROPERTY_RECIPIENT, $this->get_user_id());
        $conditions[] = new EqualityCondition(PersonalMessengerPublication :: PROPERTY_USER, $this->get_user_id());
        return new AndCondition($conditions);
    }

    function get_publication_viewing_link($personal_message)
    {
        return $this->get_link(PersonalMessengerManager :: APPLICATION_NAME, array(PersonalMessengerManager :: PARAM_ACTION => PersonalMessengerManager :: ACTION_VIEW_PUBLICATION, PersonalMessengerManager :: PARAM_PERSONAL_MESSAGE_ID => $personal_message->get_id()));
    }
}
?>