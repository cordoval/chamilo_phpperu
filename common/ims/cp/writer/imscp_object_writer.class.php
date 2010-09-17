<?php

/**
 * Used to write object's to the CEO format.
 * One object per file.
 * 
 * 
 * @copyright (c) 2010 University of Geneva 
 * 
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class ImscpObjectWriter extends ImsXmlWriter
{
    
    private $objects=null;

    public function get_format_name(){
    	return Ceo::get_format_name();
    } 
    
    public function get_format_version(){
    	return Ceo::get_format_version();
    }
    
    public function get_format_full_name(){
    	return Ceo::get_format_full_name();
    }
    
    public function get_export(){
    	$result = $this->get_root();
    	if(empty($result)){
    		$result = $this->add_export();
    	}else{
    		$result = $this->copy($result);
    	}
    	return $result;
    }
    
    public function add_export(){
    	$result = $this->add('export');
        $result->set_attribute('formatname', $this->get_format_name());
        $result->set_attribute('formatversion', $this->get_format_version());
        $result->set_attribute('identifier', $this->create_unique_id()); 
        $result->set_attribute('created', self::format_datetime());
        $result->set_attribute('source', $_SERVER['SERVER_NAME']);
    	$result->set_attribute('xmlns', 'http://www.chamilo.org/xsd/ceo_v1p0');
        return $result;
    }
    
 
    public function get_objects(){
    	if(empty($this->objects)){
    		$this->objects = $this->get_export()->add_objects();
    	}
    	return $this->objects;
    }

    /**
     * 
     * @return ImscpObjectWriter
     */
    public function add_objects(){
    	return $this->add('objects');
    }
    
    /**
     * 
     * @param $id
     * @param $is_complex
     * @return ImscpObjectWriter
     */
    public function add_object($catalog, $id, $type){
    	$result = $this->add('object');
        $result->set_attribute('catalog', $catalog);
        $result->set_attribute('id', $id);
        $result->set_attribute('type', $type);
        return $result;
    }

    /**
     * 
     * @return ImscpObjectWriter
     */
    public function add_general(){
    	return $this->add('general');
    }
  
    /**
     * 
     * @return ImscpObjectWriter
     */
    public function add_identifiers(){
    	return $this->add('identifiers');
    }

    /**
     * 
     * @param $catalog
     * @param $entry
     * @return ImscpObjectWriter
     */
    public function add_identifier($catalog, $entry){
    	$result = $this->add('identifier');
    	$result->add_catalog($catalog);
    	$result->add_entry($entry);
    	return $result;
    }
    
    /**
     * 
     * @param $catalog
     * @return ImscpObjectWriter
     */
    public function add_catalog($catalog){
    	$result = $this->add('catalog', $catalog);	
    }
    
    /**
     * @param unknown_type $entry
     * @return ImscpObjectWriter
     */
    public function add_entry($entry){
    	$result = $this->add('entry', $entry);	
    }

    /**
     * 
     * @return ImscpObjectWriter
     */
    public function add_extended(){
    	return $this->add('extended');
    }

    /**
     * 
     * @return ImscpObjectWriter
     */
    public function add_attachments(){
    	$result = $this->add('attachments');
    	return $result;
    }
    
    /**
     * 
     * @param $idref
     * @return ImscpObjectWriter
     */
    public function add_attachment($href, $idref){
    	$result = $this->add('attachment');
        $result->set_attribute('href', $href);
        $result->set_attribute('idref', $idref);
        return $result;
    }
    
    /**
     * 
     * @return ImscpObjectWriter
     */
    public function add_includes(){
    	$result = $this->add('includes');
    	return $result;
    }
    
    /**
     * 
     * @param string $idref
     * @return ImscpObjectWriter
     */
    public function add_include($href, $idref){
    	$result = $this->add('include');
        $result->set_attribute('href', $href);
        $result->set_attribute('idref', $idref);
        return $result;
    }

    /**
     * 
     * @return ImscpObjectWriter
     */
    public function add_subItems(){
    	return $this->add('subItems');
    }
    
    /**
     * 
     * @param unknown_type $idref
     * @param unknown_type $id
     * @return ImscpObjectWriter
     */
    public function add_subItem($href, $idref, $id){
    	$result = $this->add('subItem');
        $result->set_attribute('href', $href);
        $result->set_attribute('idref', $idref);
        $result->set_attribute('id', $id);
        return $result;
    }
    
    /**
     * 
     * @return ImscpObjectWriter
     */
    public function add_publications(){
    	return $this->add('publications');
    }
    
    /**
     * 
     * @param unknown_type $idref
     * @param unknown_type $id
     * @return ImscpObjectWriter
     */
    public function add_publication($href, $publication_id, $object_id){
    	$result = $this->add('publication');
        $result->set_attribute('href', $href);
        $result->set_attribute('id', $publication_id);
        $result->set_attribute('idref', $object_id);
        return $result;
    }
    
    /**
     * 
     * @return ImscpObjectWriter
     */
    public function add_categories(){
    	return $this->add('categories');
    }
    
    /**
     * 
     * @param $id
     * @param $name
     * @param $parent_id
     * @return ImscpObjectWriter
     */
    public function add_category($id, $name){
    	$result = $this->add('category');
    	$result->add('id', $id);
    	$result->add('name', $name);
        return $result;
    }
    
    /**
     * 
     * @return ImscpObjectWriter
     * @param $value
     */
    public function add_parent($value){
    	return $this->add('parent', $value);
    }
    
    /**
     * 
     * @return ImscpObjectWriter
     */
    public function add_user_relations(){
    	$result = $this->add('user_relations');
        return $result;
    }
    
 	/**
     * 
     * @param string $id
     * @param string $idref
     * @return ImscpObjectWriter
     */
    public function add_user_relation($object_id, $user_id, $user_email, $user_code){
    	$result = $this->add('user_relation');
        $result->set_attribute('object_id', $object_id);
        $result->set_attribute('user_id', $user_id);
        $result->set_attribute('user_email', $user_email);
        $result->set_attribute('user_code', $user_code);
        return $result;
    }
    
    /**
     * 
     * @param string $id
     * @param string $idref
     * @return ImscpObjectWriter
     */
    public function add_user($id){
    	$result = $this->add('user');
        $result->set_attribute('id', $id);
        return $result;
    }
    
    /**
     * 
     * @return ImscpObjectWriter
     */
    public function add_sections(){
    	$result = $this->add('sections');
        return $result;
    }
    
    /**
     * 
     * @param $id
     * @return ImscpObjectWriter
     */
    public function add_section($id){
    	$result = $this->add('section');
        $result->set_attribute('id', $id);
        return $result;
    }
    
	/**
     * 
     * @return ImscpObjectWriter
     */
    public function add_modules(){
    	$result = $this->add('modules');
        return $result;
    } 
    
    /**
     * 
     * @param $id
     * @return ImscpObjectWriter
     */
    public function add_module($id='', $name=''){
    	$result = $this->add('module');
        $result->set_attribute('id', $id, false);
        $result->set_attribute('name', $name, false);
        return $result;
    }
    
    /**
     * 
     * @return ImscpObjectWriter
     */
	public function add_groups(){
    	$result = $this->add('groups');
        return $result;
    }
    
    /**
     * 
     * @param $id
     * @return ImscpObjectWriter
     */
    public function add_group($id){
    	$result = $this->add('group');
        $result->set_attribute('id', $id);
        return $result;
    }

 /**
     * 
     * @return ImscpObjectWriter
     */
    public function add_settings(){
    	$result = $this->add('settings');
        return $result;
    } 
    
    /**
     * 
     * @return ImscpObjectWriter
     */
    public function add_layout_settings(){
    	$result = $this->add('layout_settings');
        return $result;
    }
    
    /**
     * 
     * @return ImscpObjectWriter
     */
    public function add_rights(){
    	$result = $this->add('rights');
        return $result;
    	
    }
    
     /**
     * 
     * @return ImscpObjectWriter
     */
    public function add_introduction($xml){
    	$result = $this->add('introduction');
    	$result->add_xml($xml);
        return $result;
    }
    
    /**
     * 
     * @return ImscpObjectWriter
     */
    public function add_type($id, $name){
    	$result = $this->add('type');
        $result->set_attribute('id', $id);
        $result->set_attribute('name', $name);
        return $result;
    }
    
}







?>