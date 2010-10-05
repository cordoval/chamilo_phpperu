<?php

require_once Path :: get_application_path() . 'internship_organizer/php/internship_organizer_manager/internship_organizer_manager.class.php';
require_once Path :: get_application_path() . 'internship_organizer/php/period_manager/component/viewer.class.php';


class InternshipOrganizerPeriodManagerPublicationDeleterComponent extends InternshipOrganizerPeriodManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $period_id = $_GET[self :: PARAM_PERIOD_ID];
    	$ids = $_GET[self :: PARAM_PUBLICATION_ID];
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                
                if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_DELETE , $id, InternshipOrganizerRights :: TYPE_PUBLICATION))
                {
                    $publication = InternshipOrganizerDataManager::get_instance()->retrieve_publication($id);
                    
                    if (! $publication->delete())
                    {
                        $failures ++;
                    }
                }
            
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipOrganizerPublicationNotDeleted';
                }
                else
                {
                    $message = 'SelectedInternshipOrganizerPublicationsNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipOrganizerPublicationDeleted';
                }
                else
                {
                    $message = 'SelectedInternshipOrganizerPublicationsDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(self :: PARAM_ACTION => self :: ACTION_VIEW_PERIOD, self :: PARAM_PERIOD_ID => $period_id, DynamicTabsRenderer::PARAM_SELECTED_TAB => InternshipOrganizerPeriodManagerViewerComponent :: TAB_PUBLICATIONS));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerPublicationsSelected')));
        }
    }
}
?>