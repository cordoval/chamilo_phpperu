<?php

require_once Path :: get_repository_path().'lib/data_manager/database_repository_data_manager.class.php';

abstract class SurveyContextDataManager extends DataBaseRepositoryDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor.
     */
    protected function SurveyContextDataManager()
    {
      	$this->initialize();
    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return SurveyContextDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__)  .'/'. strtolower($type) . '.class.php';
            $class = $type . 'SurveyContextDataManager';
			self :: $instance = new $class();
        }
        return self :: $instance;
    }


    abstract function retrieve_survey_contexts($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function retrieve_survey_context_by_id($context_id, $type);

    abstract function delete_survey_context($survey_context);

    abstract function update_survey_context($survey_context);

    abstract function create_survey_context($survey_context);

    abstract function count_survey_context($condition = null);

    abstract function retrieve_additional_survey_context_properties($survey_context);


}
?>