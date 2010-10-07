<?php
/**
 * $Id: default_pm_publication_table_column_model.class.php 203 2009-11-13 12:46:38Z chellee $
 * @package application.personal_messenger.pm_publication_table
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once WebApplication :: get_application_class_lib_path('personal_messenger') . 'personal_message_publication.class.php';

class DefaultPmPublicationTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultPmPublicationTableColumnModel($folder)
    {
        parent :: __construct(self :: get_default_columns($folder), 1);
    }

    /**
     * Gets the default columns for this model
     * @return ContentObjectTableColumn[]
     */
    private static function get_default_columns($folder)
    {
        $columns = array();
        $columns[] = new ObjectTableColumn(PersonalMessagePublication :: PROPERTY_STATUS);
        $columns[] = new ObjectTableColumn(PersonalMessagePublication :: PROPERTY_PERSONAL_MESSAGE);
        
        switch ($folder)
        {
            case PersonalMessengerManager :: FOLDER_INBOX :
                $columns[] = new ObjectTableColumn(PersonalMessagePublication :: PROPERTY_SENDER);
                break;
            case PersonalMessengerManager :: FOLDER_OUTBOX :
                $columns[] = new ObjectTableColumn(PersonalMessagePublication :: PROPERTY_RECIPIENT);
                break;
            default :
                $columns[] = new ObjectTableColumn(PersonalMessagePublication :: PROPERTY_SENDER);
        }
        
        $columns[] = new ObjectTableColumn(PersonalMessagePublication :: PROPERTY_PUBLISHED);
        return $columns;
    }
}
?>