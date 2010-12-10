<?php
namespace application\phrases;

use common\libraries\ObjectTableCellRenderer;
use repository\ContentObject;
use common\libraries\Utilities;
use common\libraries\Translation;
use repository\content_object\adaptive_assessment\AdaptiveAssessment;

/**
 * @author Hans De Bisschop
 * @package application.phrases
 */

class DefaultPhrasesPublicationTableCellRenderer extends ObjectTableCellRenderer
{

    private static $publication_object_cache;

    /**
     * Constructor
     */
    function __construct()
    {

    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param PhrasesPublication $phrases_publication - The phrases_publication
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $phrases_publication)
    {
        if (! self :: $publication_object_cache || self :: $publication_object_cache->get_id() != $phrases_publication->get_content_object())
        {
            $content_object = $phrases_publication->get_publication_object();
            self :: $publication_object_cache = $content_object;
        }
        else
        {
            $content_object = self :: $publication_object_cache;
        }
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TITLE :

                if ($phrases_publication->get_hidden())
                {
                    return '<span style="color: #999999;">' . $content_object->get_title() . '</span>';
                }

                return $content_object->get_title();
            case ContentObject :: PROPERTY_DESCRIPTION :
                $description = Utilities :: truncate_string($content_object->get_description(), 200);

                if ($phrases_publication->get_hidden())
                {
                    return '<span style="color: #999999;">' . $description . '</span>';
                }

                return $description;
            case PhrasesPublication :: PROPERTY_FROM_DATE :
                return $phrases_publication->get_from_date();
            case PhrasesPublication :: PROPERTY_TO_DATE :
                return $phrases_publication->get_to_date();
            case PhrasesPublication :: PROPERTY_PUBLISHER :
                return $phrases_publication->get_publisher();
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