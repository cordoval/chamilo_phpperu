<?php
/**
 * $Id: action_selection_build_wizard_page.class.php 141 2009-11-10 07:44:45Z kariboe $
 * @package home.lib.home_manager.component.wizards.build
 */
require_once dirname(__FILE__) . '/build_wizard_page.class.php';
/**
 * This form can be used to let the user select the action.
 */
class ActionSelectionBuildWizardPage extends BuildWizardPage
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
    
    const ACTION_BUILD = 5;

    function buildForm()
    {
        $this->addElement('radio', 'action', Translation :: get('BuildUsers'), Translation :: get('Build'), self :: ACTION_BUILD);
        $this->addRule('action', Translation :: get('ThisFieldIsRequired'), 'required');
        $prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next') . ' >>');
        $this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
        $this->setDefaultAction('next');
        $this->_formBuilt = true;
    }
    //	/**
//	 * Determines if an action is available
//	 * @param const $action On of the actions defined in this class
//	 * @return boolean True of the given action is available in the current
//	 * course.
//	 */
//	private function is_available($action)
//	{
//		$dm = WeblcmsDatamanager::get_instance();
//		switch($action)
//		{
//			case self::ACTION_BACKUP:
//			case self::ACTION_EMPTY:
//				if($dm->count_content_object_publications($this->get_parent()->get_course_id()) == 0)
//				{
//					return false;
//				}
//				return true;
//			case self::ACTION_COPY:
//				if($dm->count_content_object_publications($this->get_parent()->get_course_id()) == 0)
//				{
//					return false;
//				}
//				$condition = new EqualityCondition(CourseUserRelation :: PROPERTY_STATUS, 1);
//				if($dm->count_course_user_relations($condition) <= 1)
//				{
//					return false;
//				}
//				return true;
//		}
//	}
}
?>