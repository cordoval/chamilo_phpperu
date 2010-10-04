<?php
/**
 * $Id: deleter.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component
 */
/**
 * @author Michael Kyndt
 */

class ComplexDisplayComponentDeleterComponent extends ComplexDisplayComponent
{

    function run()
    {
        if ($this->is_allowed(DELETE_RIGHT))
        {
            /*if (Request :: get('selected_cloi'))
            {
                $cloi_ids = Request :: get('selected_cloi');
            }
            else
            {
                $cloi_ids = $_POST['selected_cloi'];
            }*/
        	
        	$complex_content_object_item_ids = Request :: get(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);

        	if (! is_array($complex_content_object_item_ids))
            {
                $complex_content_object_item_ids = array($complex_content_object_item_ids);
            }

            foreach ($complex_content_object_item_ids as $complex_content_object_item_id)
            {
                $complex_content_object_item = new ComplexContentObjectItem();
                $complex_content_object_item->set_id($complex_content_object_item_id);
                $complex_content_object_item->delete();
            }
            
            if (count($complex_content_object_item_ids) > 1)
            {
                $message = htmlentities(Translation :: get('ComplexContentObjectItemsDeleted'));
            }
            else
            {
                $message = htmlentities(Translation :: get('ComplexContentObjectItemDeleted'));
            }
            
            $this->redirect($message, false, array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT, 
            				ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id()));
        }
    }

}
?>