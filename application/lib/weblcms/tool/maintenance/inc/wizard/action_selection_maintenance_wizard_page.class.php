<?php
/**
 * $Id: action_selection_maintenance_wizard_page.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.maintenance.inc.wizard
 */
require_once dirname(__FILE__) . '/maintenance_wizard_page.class.php';
/**
 * This form can be used to let the user select the action.
 */
class ActionSelectionMaintenanceWizardPage extends MaintenanceWizardPage
{
    /**
     * Constant defining the action to remove all publications from a course
     */
    const ACTION_EMPTY = 1;
    /**
     * Constant defining the action to copy publications form a course to one or
     * more other courses
     */
    const ACTION_COPY = 2;
    /**
     * Constant defining the action to create a backup of a course
     */
    const ACTION_BACKUP = 3;
    /**
     * Constant defining the action to completely remove a course
     */
    const ACTION_DELETE = 4;

    function buildForm()
    {
        $available = $this->is_available(self :: ACTION_EMPTY) ? null : 'disabled';
        $this->addElement('radio', 'action', Translation :: get('EmptyThisCourse'), Translation :: get('EmptyThisCourseInformation'), self :: ACTION_EMPTY, $available);
        $available = $this->is_available(self :: ACTION_COPY) ? null : 'disabled';
        $this->addElement('radio', 'action', Translation :: get('CopyThisCourse'), Translation :: get('CopyThisCourseInformation'), self :: ACTION_COPY, $available);
        $available = $this->is_available(self :: ACTION_BACKUP) ? null : 'disabled';
        //$this->addElement('radio', 'action', Translation :: get('BackupThisCourse'), Translation :: get('BackupThisCourseInformation'),self::ACTION_BACKUP,$available);
        $this->addElement('radio', 'action', Translation :: get('DeleteThisCourse'), Translation :: get('DeleteThisCourseInformation'), self :: ACTION_DELETE);
        $this->addRule('action', Translation :: get('ThisFieldIsRequired'), 'required');
        $prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next') . ' >>');
        $this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
        $this->setDefaultAction('next');
        $this->_formBuilt = true;
    }

    /**
     * Determines if an action is available
     * @param const $action On of the actions defined in this class
     * @return boolean True of the given action is available in the current
     * course.
     */
    private function is_available($action)
    {
        $dm = WeblcmsDatamanager :: get_instance();
        switch ($action)
        {
            case self :: ACTION_BACKUP :
            case self :: ACTION_EMPTY :
                $condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_parent()->get_course_id());
                if ($dm->count_content_object_publications_new($condition) == 0)
                {
                    return false;
                }
                return true;
            case self :: ACTION_COPY :
                $condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_parent()->get_course_id());
                if ($dm->count_content_object_publications_new($condition) == 0)
                {
                    return false;
                }
                $condition = new EqualityCondition(CourseUserRelation :: PROPERTY_STATUS, 1);
                if ($dm->count_course_user_relations($condition) <= 1)
                {
                    return false;
                }
                return true;
        }
    }
}
?>