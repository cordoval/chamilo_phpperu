<?php

/**
 * Utility class to generate unique ids. Generates either local or "unique" ids.
 * 
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class ImsIdFactory
{
    private $counter = 0;
    private $prefix = '';
    
    function __construct($prefix = ''){
    	$this->prefix = $prefix;   
    }
    
    public function create_unique_id($prefix =''){
    	$prefix = $this->get_prefix($prefix);
    	$key = $_SERVER['SERVER_ADDR']; //ideally should fetch the MAC address to get a real UUID
    	$result = $prefix . sha1(uniqid($key, true));
    	return $result;
    }

    public function create_local_id($prefix ='')
    {    	
    	$prefix = $this->get_prefix($prefix);	
    	$prefix = empty($prefix)?'':$prefix . '_';
    	return $prefix . ++$this->counter;
    }
    
    protected function get_prefix($prefix=''){
    	return empty($prefix) ? $this->prefix : $prefix;
    }
}







?>