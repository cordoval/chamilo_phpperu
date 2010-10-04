<?php

/**
 * Used to read a IMS CP manifest file.
 * 
 * @copyright (c) 2010 University of Geneva 
 * 
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class ImscpManifestReader extends ImsXmlReader{
    
	public function __construct($item='', $return_null=false){
    	parent::__construct($item, $return_null);
    }
    
    public function get_by_id($id){
		$path = '//*[@identifier="'. $id .'"]';
		return  $this->first($path);
    } 
    
    /**
     * @return ImscpManifestReader
     */
    public function navigate(){
    	$id = $this->identifierref;
    	return $this->get_by_id($id);
    }
  
    /**
     * @return ImscpManifestReader
     */
    public function get_default_organization(){
        $organizations = $this->first('/def:manifest/def:organizations');
        $default_name = $organizations->get_default();
        $path = '//def:organization[@identifier="'. $default_name .'"]';
        return $this->first($path);
    }
    
    /**
     * @return ImscpManifestReader
     */
    public function get_first_organization(){
        $path = '/def:manifest/def:organizations/';
        return $this->first_organization = $this->first($path);
    }

    /**
     * @return ImscpManifestReader
     */
    public function get_main_organization(){
    	$result = $this->get_default_organization();
    	$result = empty($result) ? $this->get_first_organization() : $result;
    	return $result;
    }
 
    /**
     * @return ImscpManifestReader
     */
    public function get_entry_file(){
    	$href = $this->get_href();

    	$files = $this->get_files();
    	foreach($files as $file){
    		if(strtolower($href) == strtolower($file->get_href())){
    			return $file;
    		}
    	}	
    	return null;
    }

    /**
     * @return ImscpManifestReader
     */
    public function get_schema_other(){
        $path ='./*[*[name() !="schemaversion"] and *[name() !="schema"]]';
    	return $this->first($path);
    }
    
    /*
    public function get_schema_lom($for){
    	$for = $this->get($for);
    	$path ='./imsmd:lom';
    	return $this->query($path, $for)->item(0);
    }
    */

}












?>