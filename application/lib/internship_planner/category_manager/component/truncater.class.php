<?php
/**
 * $Id: truncater.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package category.lib.category_manager.component
 */

class InternshipPlannerCategoryManagerTruncaterComponent extends InternshipPlannerCategoryManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $user = $this->get_user();
               
        $ids = Request :: get(InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $category = $this->retrieve_category($id);
                if (! $category->truncate())
                {
                    $failures ++;
                }
//                else
//                {
//                    Events :: trigger_event('empty', 'category', array('target_category_id' => $category->get_id(), 'action_user_id' => $user->get_id()));
//                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipPlannerCategoryNotEmptied';
                }
                else
                {
                    $message = 'SelectedInternshipPlannerCategorysNotEmptied';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipPlannerCategoryEmptied';
                }
                else
                {
                    $message = 'SelectedInternshipPlannerCategorysEmptied';
                }
            
            }
            
            if (count($ids) == 1)
                $this->redirect(Translation :: get($message), ($failures ? true : false), array(InternshipPlannerCategoryManager :: PARAM_ACTION => InternshipPlannerCategoryManager :: ACTION_VIEW_CATEGORY, InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID => $ids[0]));
            else
                $this->redirect(Translation :: get($message), ($failures ? true : false), array(InternshipPlannerCategoryManager :: PARAM_ACTION => InternshipPlannerCategoryManager :: ACTION_BROWSE_CATEGORIES));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipPlannerCategorySelected')));
        }
    }
}
?>