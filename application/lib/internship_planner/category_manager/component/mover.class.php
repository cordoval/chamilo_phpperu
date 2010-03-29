<?php

require_once Path :: get_application_path() . 'lib/internship_planner/forms/category_move_form.class.php';


class InternshipPlannerCategoryManagerMoverComponent extends InternshipPlannerCategoryManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
      
        $trail->add_help('category general');
                
        $category = $this->retrieve_categories(new EqualityCondition(InternshipPlannerCategory :: PROPERTY_ID, Request :: get(InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID)))->next_result();
        
        $trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerCategoryManager :: PARAM_ACTION => InternshipPlannerCategoryManager :: ACTION_VIEW_CATEGORY, InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID => Request :: get(InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID))), $category->get_name()));
        
        $form = new InternshipPlannerCategoryMoveForm($category, $this->get_url(array(InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID => Request :: get(InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID))), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->move_category();
            $parent = $form->get_new_parent();
            $this->redirect($success ? Translation :: get('InternshipPlannerCategoryMoved') : Translation :: get('InternshipPlannerCategoryNotMoved'), $success ? (false) : true, array(InternshipPlannerCategoryManager :: PARAM_ACTION => InternshipPlannerCategoryManager :: ACTION_BROWSE_CATEGORIES, InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID => $parent));
        }
        else
        {
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Move')));
            $this->display_header($trail);
            echo Translation :: get('InternshipPlannerCategory') . ': ' . $category->get_name();
            $form->display();
            $this->display_footer();
        }
    }
}
?>