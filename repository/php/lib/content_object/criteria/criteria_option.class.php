<?php
/**
 *  $Id: criteria_option.class.php  $
 *  @package repository.lib.content_object.criteria
 *  @author Sven Vanpoucke
 */

/**
 * This class represents an option in a criteria
 */
class CriteriaOption
{    
    private $description;
	private $score;
    
    function CriteriaOption($description, $score)
    {
		$this->description = $description;
    	$this->score = $score;
    }
    
	function get_description()
    {
    	return $this->description;
    } 
    
	function get_score()
    {
    	return $this->score;
    }
    
	function set_description($description)
    {
    	$this->description = $description;
    } 
    
	function set_score($score)
    {
    	$this->score = $score;
    }
    
}
?>