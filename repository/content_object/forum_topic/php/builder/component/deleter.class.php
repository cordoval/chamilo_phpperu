<?php
namespace repository\content_object\forum_topic;

use repository\RepositoryDataManager;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use repository\ComplexBuilder;
use repository\ContentObject;
/**
 * $Id: deleter.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.component
 */
class ForumTopicBuilderDeleterComponent extends ForumTopicBuilder
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(ComplexBuilder :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);

        $failures = 0;

        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            $repositor_data_manager = RepositoryDataManager :: get_instance();

            foreach ($ids as $complex_content_object_item_id)
            {
                $complex_content_object_item = $repositor_data_manager->retrieve_complex_content_object_item($complex_content_object_item_id);

                if ($complex_content_object_item->get_user_id() == $this->get_user_id())
                {
                    // TODO: check if deletion is allowed
                    //if ($this->get_parent()->complex_content_object_item_deletion_allowed($complex_content_object_item))
                    {
                        if (! $complex_content_object_item->delete())
                        {
                            $failures ++;
                        }
                    }
                }
                else
                {
                    $failures ++;
                }
            }

            if ($parent == $root)
                $parent = null;

            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = Translation :: get('ObjectNotDeleted', array('OBJECT' => Translation :: get('ForumTopic')), Utilities :: COMMON_LIBRARIES);
                }
                else
                {
                    $message = Translation :: get('ObjectsNotDeleted', array('OBJECT' => Translation :: get('ForumTopic')), Utilities :: COMMON_LIBRARIES);
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = Translation :: get('ObjectDeleted', array('OBJECT' => Translation :: get('ForumTopic')), Utilities :: COMMON_LIBRARIES);
                }
                else
                {
                    $message = Translation :: get('ObjectsDeleted', array('OBJECT' => Translation :: get('ForumTopics')), Utilities :: COMMON_LIBRARIES);
                }
            }

            $count = $repositor_data_manager->count_content_objects(new EqualityCondition(ContentObject :: PROPERTY_ID, $this->get_root_content_object_id()));

            if ($count == 1)
            {
                $this->redirect($message, $failures ? true : false, array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE));
            }
            else
            {
                $this->redirect_away_from_complex_builder(Translation :: get($message), $failures ? true : false);
            }
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected', null , Utilities :: COMMON_LIBRARIES)));
        }
    }
}
?>