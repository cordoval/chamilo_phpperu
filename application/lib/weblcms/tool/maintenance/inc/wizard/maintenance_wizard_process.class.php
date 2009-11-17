<?php
/**
 * $Id: maintenance_wizard_process.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.maintenance.inc.wizard
 */
/**
 * This class implements the action to take after the user has completed a
 * course maintenance wizard
 */
class MaintenanceWizardProcess extends HTML_QuickForm_Action
{
    /**
     * The repository tool in which the wizard runs.
     */
    private $parent;

    /**
     * Constructor
     * @param Tool $parent The repository tool in which the wizard
     * runs.
     */
    public function MaintenanceWizardProcess($parent)
    {
        $this->parent = $parent;
    }

    function perform($page, $actionName)
    {
        $values = $page->controller->exportValues();
        //Todo: Split this up in several form-processing classes depending on selected action
        switch ($values['action'])
        {
            case ActionSelectionMaintenanceWizardPage :: ACTION_EMPTY :
                $publication_ids = array_keys($values['publications']);
                $dm = WeblcmsDataManager :: get_instance();
                $succes = true;
                
                foreach ($publication_ids as $id)
                {
                    $publication = $dm->retrieve_content_object_publication($id);
                    if (! $dm->delete_content_object_publication($publication))
                    {
                        $succes = false;
                    }
                }
                
                $course_section_ids = array_keys($values['course_sections']);
                $condition = new InCondition(CourseSection :: PROPERTY_ID, $course_section_ids);
                $course_sections = $dm->retrieve_course_sections($condition);
                while ($course_section = $course_sections->next_result())
                {
                    if (! $course_section->delete())
                    {
                        $succes = false;
                    }
                }
                
                if ($values['content_object_categories'] == 1)
                {
                    $condition = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_COURSE, $this->parent->get_course_id());
                    $categories = $dm->retrieve_content_object_publication_categories($condition);
                    while ($category = $categories->next_result())
                    {
                        if (! $category->get_allow_change())
                            continue;
                        
                        if (! $category->delete())
                        {
                            $succes = false;
                        }
                    }
                }
                
                if ($succes)
                {
                    $_SESSION['maintenance_message'] = Translation :: get('AllSelectedObjectsRemoved');
                }
                else
                {
                    $_SESSION['maintenance_error_message'] = Translation :: get('NotAllSelectedObjectsRemoved');
                }
                break;
            case ActionSelectionMaintenanceWizardPage :: ACTION_COPY :
                
                $dm = WeblcmsDataManager :: get_instance();
                
                $course_section_ids = array_keys($values['course_sections']);
                $condition = new InCondition(CourseSection :: PROPERTY_ID, $course_section_ids);
                $course_sections = $dm->retrieve_course_sections($condition);
                while ($course_section = $course_sections->next_result())
                {
                    $courses = $values['course'];
                    foreach ($courses as $course_code)
                    {
                        $course_section->set_id(null);
                        $course_section->set_course_code($course_code);
                        $course_section->create();
                    }
                }
                
                $category_ids = array();
                
                if ($values['content_object_categories'] == 1)
                {
                    $condition = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_COURSE, $this->parent->get_course_id());
                    $categories = $dm->retrieve_content_object_publication_categories($condition);
                    while ($category = $categories->next_result())
                    {
                        if (! $category->get_allow_change())
                            continue;
                        
                        $courses = $values['course'];
                        $parent = $category->get_parent();
                        $id = $category->get_id();
                        
                        foreach ($courses as $course_code)
                        {
                            
                            $category->set_id(null);
                            $category->set_course($course_code);
                            
                            if ($parent != 0)
                            {
                                $category->set_parent($category_ids[$parent]['course_code']);
                            }
                            
                            $category->create();
                            $category_ids[$id]['course_code'] = $category->get_id();
                        }
                    }
                }
                
                $publication_ids = array_keys($values['publications']);
                foreach ($publication_ids as $id)
                {
                    $publication = $dm->retrieve_content_object_publication($id);
                    $courses = $values['course'];
                    $parent = $publication->get_category_id();
                    
                    foreach ($courses as $course_code)
                    {
                        $publication->set_id(null);
                        $publication->set_course_id($course_code);
                        
                        if ($parent != 0)
                        {
                            $publication->set_category_id($category_ids[$parent]['course_code']);
                        }
                        
                        $publication->create();
                    }
                }
                
                $_SESSION['maintenance_message'] = Translation :: get('CopyFinished');
                break;
            case ActionSelectionMaintenanceWizardPage :: ACTION_BACKUP :
                $_SESSION['maintenance_error_message'] = 'BACKUP: TODO';
                break;
            case ActionSelectionMaintenanceWizardPage :: ACTION_DELETE :
                $dm = WeblcmsDatamanager :: get_instance();
                $dm->delete_course($this->parent->get_course_id());
                header('Location: ' . $this->parent->get_path(WEB_PATH) . 'run.php?application=weblcms');
                exit();
                break;
        }
        $page->controller->container(true);
        $page->controller->run();
    }
}
?>