<?php
/**
 * $Id: assessment_publication_category.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.category_manager
 */
require_once Path :: get_application_library_path() . 'category_manager/platform_category.class.php';
require_once dirname(__FILE__) . '/../assessment_data_manager.class.php';

/**
 * @package category
 */
/**
 *	@author Sven Vanpoucke
 */

class AssessmentPublicationCategory extends PlatformCategory
{

    function create()
    {
        $adm = AssessmentDataManager :: get_instance();
        $this->set_display_order($adm->select_next_assessment_publication_category_display_order($this->get_parent()));
        return $adm->create_assessment_publication_category($this);
    }

    function update()
    {
        return AssessmentDataManager :: get_instance()->update_assessment_publication_category($this);
    }

    function delete()
    {
        return AssessmentDataManager :: get_instance()->delete_assessment_publication_category($this);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(__CLASS__);
    }
}