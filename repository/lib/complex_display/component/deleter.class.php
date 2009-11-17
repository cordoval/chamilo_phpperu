<?php
/**
 * $Id: deleter.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component
 */
/**
 * @author Michael Kyndt
 */

class ComplexDisplayDeleterComponent extends ComplexDisplayComponent
{

    function run()
    {
        if ($this->is_allowed(DELETE_RIGHT))
        {
            if (Request :: get('selected_cloi'))
            {
                $cloi_ids = Request :: get('selected_cloi');
            }
            else
            {
                $cloi_ids = $_POST['selected_cloi'];
            }
            
            if (! is_array($cloi_ids))
            {
                $cloi_ids = array($cloi_ids);
            }
            
            foreach ($cloi_ids as $cid)
            {
                {
                    $cloi = new ComplexContentObjectItem();
                    $cloi->set_id($cid);
                    $cloi->delete();
                }
            
            }
            
            if (count($cloi_ids) > 1)
            {
                $message = htmlentities(Translation :: get('ComplexContentObjectItemsDeleted'));
            }
            else
            {
                $message = htmlentities(Translation :: get('ComplexContentObjectItemDeleted'));
            }
            
            $this->redirect($message, false, array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_VIEW_CLO, 'pid' => Request :: get('pid'), 'cid' => Request :: get('cid')));
        }
    }

}
?>
