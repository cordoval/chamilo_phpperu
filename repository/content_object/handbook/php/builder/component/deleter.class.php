<?php
namespace repository\content_object\handbook;

use repository\content_object\handbook_item\ComplexHandbookItem;
use common\libraries\Request;
use repository\ComplexBuilderComponent;
use repository\ComplexBuilder;
use repository\RepositoryDataManager;
use common\libraries\Translation;
use common\libraries\Utilities;

require_once dirname(__FILE__) . '/../handbook_builder.class.php';

/**
 */
class HandbookBuilderDeleterComponent extends HandbookBuilder
{
    
    private $complex_builder_deleter_component;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->complex_builder_deleter_component = ComplexBuilderComponent :: factory(ComplexBuilderComponent :: DELETER_COMPONENT, $this);
        $ids = Request :: get(ComplexBuilder :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        $root = $this->get_root_content_object();
        $parent_complex_content_object = Request :: get(ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            $rdm = RepositoryDataManager :: get_instance();
            
            foreach ($ids as $complex_content_object_item_id)
            {
                $complex_content_object_item = $rdm->retrieve_complex_content_object_item($complex_content_object_item_id);
                
                if ($complex_content_object_item->get_user_id() == $this->get_user_id())
                {
                    // TODO: check if deletion is allowed
                    //if ($this->get_parent()->complex_content_object_item_deletion_allowed($cloi))
                    {
                        if (! $complex_content_object_item->delete())
                        {
                            $failures ++;
                        }
                        else
                        {
                            if ($complex_content_object_item instanceof ComplexHandbookItem)
                            {
                                $rdm->delete_content_object_by_id($complex_content_object_item->get_ref());
                            }
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
                    $message = 'ObjectNotDeleted';
                }
                else
                {
                    $message = 'ObjectsNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'ObjectDeleted';
                }
                else
                {
                    $message = 'ObjectsDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message, array ('OBJECT' => Translation :: get('handbook')), Utilities :: COMMON_LIBRARIES), $failures ? true : false, array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE, ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $parent_complex_content_object_item));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected', null, Utilities :: COMMON_LIBRARIES)));
        }
    }
}
?>