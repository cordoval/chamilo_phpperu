<?php
require_once Path :: get_application_path() . 'lib/internship_organizer/forms/category_form.class.php';


class InternshipOrganizerCategoryManagerCreatorComponent extends SurveyBuilderComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_browse_categories_url(), Translation :: get('BrowseCategories')));
        
        $trail->add(new Breadcrumb($this->get_category_create_url, Translation :: get('CreateCategory')));
       
             
        $category = new InternshipOrganizerCategory();
        $category->set_parent_id(Request :: get(InternshipOrganizerCategoryManager :: PARAM_CATEGORY_ID));
        $form = new InternshipOrganizerCategoryForm(InternshipOrganizerCategoryForm :: TYPE_CREATE, $category, $this->get_url(array(SurveyBuilderComponent :: PARAM_CATEGORY_ID => Request :: get(SurveyBuilderComponent :: PARAM_CATEGORY_ID))), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_category();
            if ($success)
            {
                $category = $form->get_category();
                $this->redirect(Translation :: get('InternshipOrganizerCategoryCreated'), (false), array(SurveyBuilderComponent :: PARAM_ACTION => SurveyBuilderComponent :: ACTION_VIEW_CATEGORY, SurveyBuilderComponent :: PARAM_CATEGORY_ID => $category->get_id()));
            }
            else
            {
                $this->redirect(Translation :: get('InternshipOrganizerCategoryNotCreated'), (true), array(SurveyBuilderComponent :: PARAM_ACTION => SurveyBuilderComponent :: ACTION_BROWSE_CATEGORIES));
            }
        }
        else
        {
            $this->display_header($trail, false);
            $form->display();
            $this->display_footer();
        }
    }
}
?>