<?php
/**
 * $Id: dokeos185_quiz_rel_question.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_quiz_rel_question.class.php';

/**
 * This class presents a Dokeos185 quiz_rel_question
 *
 * @author Sven Vanpoucke
 */
class Dokeos185QuizRelQuestion extends MigrationDataClass
{
    /**
     * Dokeos185QuizRelQuestion properties
     */
    const PROPERTY_QUESTION_ID = 'question_id';
    const PROPERTY_EXERCICE_ID = 'exercice_id';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185QuizRelQuestion object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185QuizRelQuestion($defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets a default property by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gets the default properties
     * @return array An associative array containing the properties.
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_QUESTION_ID, self :: PROPERTY_EXERCICE_ID);
    }

    /**
     * Sets a default property by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Sets the default properties of this class
     */
    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Returns the question_id of this Dokeos185QuizRelQuestion.
     * @return the question_id.
     */
    function get_question_id()
    {
        return $this->get_default_property(self :: PROPERTY_QUESTION_ID);
    }

    /**
     * Returns the exercice_id of this Dokeos185QuizRelQuestion.
     * @return the exercice_id.
     */
    function get_exercice_id()
    {
        return $this->get_default_property(self :: PROPERTY_EXERCICE_ID);
    }

    /**
     * Gets all the quiz rel question of a course
     * @param Array $array
     * @return Array of dokeos185quizrelquestion
     */
    static function retrieve_data($parameters)
    {
        self :: $mgdm = $parameters['mgdm'];
        
        if ($array['del_files'] = ! 1)
            $tool_name = 'quiz_rel_question';
        
        $coursedb = $array['course']->get_db_name();
        $tablename = 'quiz_rel_question';
        $classname = 'Dokeos185QuizRelQuestion';
        
        return self :: $mgdm->get_all($coursedb, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'quiz_rel_question';
        return $array;
    }

}

?>