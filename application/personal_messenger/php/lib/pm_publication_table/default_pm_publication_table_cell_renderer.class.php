<?php
namespace application\personal_messenger;

use common\libraries\ObjectTableCellRenderer;
use common\libraries\Translation;

/**
 * $Id: default_pm_publication_table_cell_renderer.class.php 203 2009-11-13 12:46:38Z chellee $
 * @package application.personal_messenger.pm_publication_table
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class DefaultPmPublicationTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultPmPublicationTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $personal_message_publication)
    {
        switch ($column->get_name())
        {
            case PersonalMessengerPublication :: PROPERTY_PERSONAL_MESSAGE :
                return $personal_message_publication->get_publication_object()->get_title();
            case PersonalMessengerPublication :: PROPERTY_SENDER :
                $user = $personal_message_publication->get_publication_sender();
                if ($user)
                    return $user->get_firstname() . '&nbsp;' . $user->get_lastname();
                return Translation :: get('SenderUnknown');
            case PersonalMessengerPublication :: PROPERTY_RECIPIENT :
                $user = $personal_message_publication->get_publication_recipient();
                if ($user)
                    return $user->get_firstname() . '&nbsp;' . $user->get_lastname();
                return Translation :: get('RecipientUnknown');
            case PersonalMessengerPublication :: PROPERTY_PUBLISHED :
                return $personal_message_publication->get_published();
            case PersonalMessengerPublication :: PROPERTY_STATUS :
                return $personal_message_publication->get_status();
            default :
                return '&nbsp;';
        }
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>