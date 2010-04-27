<?php
/**
 * $Id: introduction_deleter.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/../weblcms_manager.class.php';


class WeblcmsManagerIntroductionDeleterComponent extends WeblcmsManager
{

    function run()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'introduction');
        $condition = new AndCondition($conditions);
        
        $publications = WeblcmsDataManager :: get_instance()->retrieve_content_object_publications_new($condition);
        $introduction_text = $publications->next_result();
        $introduction_text->delete();
        $this->redirect(Translation :: get('IntroductionDeleted'), '', array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_VIEW_COURSE));
    }
}
?>