<?php

namespace application\personal_messenger;

use common\libraries\WebApplication;
use common\libraries\Translation;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;

/**
 * $Id: most_recent.class.php 203 2009-11-13 12:46:38Z chellee $
 * @package application.personal_messenger.block
 */
require_once WebApplication :: get_application_class_path('personal_messenger') . 'blocks/personal_messenger_block.class.php';

/**
 * This class represents a calendar publisher component which can be used
 * to browse through the possible learning objects to publish.
 */
class PersonalMessengerMostRecent extends PersonalMessengerBlock {

    /*
     * Inherited
     */
    function display_content() {
        $html = array();

        $publications = PersonalMessengerDataManager :: get_instance()->retrieve_personal_message_publications($this->get_condition(), array(), array(), 5);

        if ($publications->size() > 0) {
            $target = $this->get_link_target();
            $target = $target ? ' target="' . $target . '" ' : '';
            $html[] = '<ul style="list-style: square inside;">';
            while ($publication = $publications->next_result()) {
                $html[] = '<li>';
                $html[] = '<a href="' . htmlspecialchars($this->get_publication_viewing_link($publication)) . '"'.$target.'>' . htmlspecialchars($publication->get_publication_object()->get_title()) . '</a>';
                $html[] = '</li>';
            }
            $html[] = '</ul>';
        } else {
            $html[] = htmlspecialchars(Translation :: get('NoNewMessages'));
        }

        return implode("\n", $html);
    }

    function get_condition() {
        $conditions = array();
        $conditions[] = new EqualityCondition(PersonalMessengerPublication :: PROPERTY_RECIPIENT, $this->get_user_id());
        $conditions[] = new EqualityCondition(PersonalMessengerPublication :: PROPERTY_USER, $this->get_user_id());
        return new AndCondition($conditions);
    }

}

?>