<?php
/*
 * @author Johan Janssens
 */
class GradeEvaluation extends DataClass
{
	const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'grade_evaluation';
    /**
     * Evaluation properties
     */
     const PROPERTY_SCORE = 'score';
     const PROPERTY_COMMENT = 'comment';
     
	static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_SCORE, self :: PROPERTY_COMMENT));
    }

    function get_data_manager()
    {
        return GradebookDataManager :: get_instance();
    }
    
    // getters and setters
	/**
    * Returns the score of this GradeEvaluation.
    * @return score.
    */	
    function get_score()
    {
    	return $this->get_default_property(self :: PROPERTY_SCORE);
    }
    
     /**
     * Sets the score of this GradeEvaluation.
     * @param user id
     */
    function set_score($score)
    {
    	$this->set_default_property(self :: PROPERTY_SCORE, $score);
    }
    
	/**
    * Returns the score of this GradeEvaluation.
    * @return score.
    */	
    function get_comment()
    {
    	return $this->get_default_property(self :: PROPERTY_COMMENT);
    }
    
     /**
     * Sets the comment of this GradeEvaluation.
     * @param comment
     */
    function set_comment($comment)
    {
    	$this->set_default_property(self :: PROPERTY_COMMENT, $comment);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>