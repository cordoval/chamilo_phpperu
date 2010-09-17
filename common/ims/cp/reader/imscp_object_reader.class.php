<?php

/**
 * Used to read a CEO object's file. 
 * 
 * 
 * @copyright (c) 2010 University of Geneva 
 * 
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class ImscpObjectReader extends ImsXmlReader{

    /**
     * id string
	 * @return ImscpObjectReader
     */
   public function get_object_by_id($id){
    	$path = '//def:object[@id="'. $id . '"]';
    	return $this->first($path);
    }
}






?>