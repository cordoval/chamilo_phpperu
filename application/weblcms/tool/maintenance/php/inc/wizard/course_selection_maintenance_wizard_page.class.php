<?php
namespace application\weblcms\tool\maintenance;

use application\weblcms\WeblcmsDataManager;

use common\libraries\Translation;
use common\libraries\Utilities;

/**
 * $Id: course_selection_maintenance_wizard_page.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.maintenance.inc.wizard
 */
require_once dirname(__FILE__) . '/maintenance_wizard_page.class.php';
/**
 * This form can be used to let the user select a course.
 */
class CourseSelectionMaintenanceWizardPage extends MaintenanceWizardPage
{

    function buildForm()
    {
        $dm = WeblcmsDataManager :: get_instance();
        $course_user_relations = $dm->retrieve_course_list_of_user_as_course_admin($this->get_parent()->get_user_id());

        $current_code = $this->get_parent()->get_course_id();

        while ($course_user_relation = $course_user_relations->next_result())
        {
            if ($course_user_relation->get_course() != $current_code)
            {
                $options[$course_user_relation->get_course()] = $dm->retrieve_course($course_user_relation->get_course())->get_name();
            }
        }

        $this->addElement('select', 'course', Translation :: get('Course'), $options, array(
                'multiple' => 'multiple',
                'size' => '20',
                'style' => 'width: 300px;'));
        $this->addRule('course', Translation :: get('Required', null, Utilities :: COMMON_LIBRARIES), 'required');
        $prevnext[] = $this->createElement('submit', $this->getButtonName('back'), '<< ' . Translation :: get('Previous', null, Utilities :: COMMON_LIBRARIES));
        $prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next', null, Utilities :: COMMON_LIBRARIES) . ' >>');
        $this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
        $this->setDefaultAction('next');
        $this->_formBuilt = true;
    }
}
?>