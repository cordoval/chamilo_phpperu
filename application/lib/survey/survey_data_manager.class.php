<?php
/**
 * $Id: survey_data_manager.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey
 */
/**
 *	This is a skeleton for a data manager for the Survey Application.
 *	Data managers must extend this class and implement its abstract methods.
 *
 *  @author Sven Vanpoucke
 *	@author
 */
abstract class SurveyDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor.
     */
    protected function SurveyDataManager()
    {
        $this->initialize();
    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return SurveyDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . Utilities :: camelcase_to_underscores($type) . '.class.php';
            $class = $type . 'SurveyDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    abstract function initialize();

    abstract function create_storage_unit($name, $properties, $indexes);

    abstract function create_survey_publication($survey_publication);

    abstract function update_survey_publication($survey_publication);

    abstract function delete_survey_publication($survey_publication);

    abstract function count_survey_publications($conditions = null);

    abstract function retrieve_survey_publication($id);

    abstract function retrieve_survey_publications($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function create_survey_publication_category($survey_category);

    abstract function update_survey_publication_category($survey_category);

    abstract function delete_survey_publication_category($survey_category);

    abstract function count_survey_publication_categories($conditions = null);

    abstract function retrieve_survey_publication_category($id);

    abstract function retrieve_survey_publication_categories($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function select_next_survey_publication_category_display_order($parent);

    abstract function create_survey_invitation($survey_invitation);

    abstract function update_survey_invitation($survey_invitation);

    abstract function delete_survey_invitation($survey_invitation);

    abstract function count_survey_invitations($conditions = null);

    abstract function retrieve_survey_invitation($id);

    abstract function retrieve_survey_invitations($condition = null, $offset = null, $count = null, $order_property = null);

}
?>