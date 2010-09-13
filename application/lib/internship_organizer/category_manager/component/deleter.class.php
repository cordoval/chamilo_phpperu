<?php

class InternshipOrganizerCategoryManagerDeleterComponent extends InternshipOrganizerCategoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
    if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_DELETE, InternshipOrganizerRights :: LOCATION_CATEGORY, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
   
    	$user = $this->get_user();
        
        $ids = Request :: get(InternshipOrganizerCategoryManager :: PARAM_CATEGORY_ID);
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
                
                if (! $category->delete())
                {
                    $failures ++;
                }
                else
                {
                    //                    Event :: trigger('delete', 'category', array('target_category_id' => $category->get_id(), 'action_user_id' => $user->get_id()));
                }
            }
            
            //$this->get_result( not good enough?
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipOrganizerCategoryNotDeleted';
                }
                else
                {
                    $message = 'SelectedInternshipOrganizerCategoriesNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipOrganizerCategoryDeleted';
                }
                else
                {
                    $message = 'SelectedInternshipOrganizerCategoriesDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(InternshipOrganizerCategoryManager :: PARAM_ACTION => InternshipOrganizerCategoryManager :: ACTION_BROWSE_CATEGORIES));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerCategorysSelected')));
        }
    }
}
?>