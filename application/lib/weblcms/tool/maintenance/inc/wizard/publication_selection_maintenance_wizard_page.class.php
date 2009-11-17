<?php
/**
 * $Id: publication_selection_maintenance_wizard_page.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.maintenance.inc.wizard
 */
require_once dirname(__FILE__) . '/maintenance_wizard_page.class.php';
/**
 * This form can be used to let the user select publications in the course.
 */
class PublicationSelectionMaintenanceWizardPage extends MaintenanceWizardPage
{

    function buildForm()
    {
        $datamanager = WeblcmsDataManager :: get_instance();
        $condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_parent()->get_course_id());
        $publications_set = $datamanager->retrieve_content_object_publications_new($condition, new ObjectTableOrder(ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX, SORT_DESC));
        while ($publication = $publications_set->next_result())
        {
            $publications[$publication->get_tool()][] = $publication;
        }
        
        $this->addElement('html', '<h3>' . Translation :: get('Publications') . '</h3>');
        
        foreach ($publications as $tool => $tool_publications)
        {
            foreach ($tool_publications as $index => $publication)
            {
                $label = $index == 0 ? Translation :: get(ucfirst($tool) . 'ToolTitle') : '';
                $content_object = $publication->get_content_object();
                $this->addElement('checkbox', 'publications[' . $publication->get_id() . ']', $label, $content_object->get_title());
            }
        }
        
        $this->addFormRule(array('PublicationSelectionMaintenanceWizardPage', 'count_selected_publications'));
        
        $this->addElement('html', '<h3>' . Translation :: get('CourseSections') . '</h3>');
        
        $condition = new EqualityCondition(CourseSection :: PROPERTY_COURSE_CODE, $this->get_parent()->get_course_id());
        $course_sections = $datamanager->retrieve_course_sections($condition);
        
        $common_sections = array(Translation :: get('Disabled'), Translation :: get('CourseAdministration'), Translation :: get('Links'), Translation :: get('Tools'));
        
        while ($course_section = $course_sections->next_result())
        {
            $label = $course_section->get_name();
            if (! in_array($label, $common_sections))
                $this->addElement('checkbox', 'course_sections[' . $course_section->get_id() . ']', $label);
        }
        
        $this->addElement('html', '<h3>' . Translation :: get('Other') . '</h3>');
        $this->addElement('checkbox', 'content_object_categories', Translation :: get('PublicationCategories'));
        
        $prevnext[] = $this->createElement('submit', $this->getButtonName('back'), '<< ' . Translation :: get('Previous'));
        $prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next') . ' >>');
        $this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
        $this->setDefaultAction('next');
        $this->_formBuilt = true;
    }

    /**
     * Returns the number of selected publications
     * @param array $values
     */
    function count_selected_publications($values)
    {
        if (isset($values['publications']) || isset($values['course_sections']))
        {
            return true;
        }
        return array('buttons' => Translation :: get('SelectPublications'));
    }
}
?>