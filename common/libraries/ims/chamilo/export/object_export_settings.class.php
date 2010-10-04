<?php

class ObjectExportSettings 
{
	private $object = null;
    private $directory = '';
    private $user = null;
    private $log = null;
    private $cache = null;
    private $manifest =null;
    private $toc = null;
    
    public function __construct($object = null, $directory = '', $manifest=null, $toc=null, $user=null, Log $log=null){
    	$this->object = $object;
		$this->directory = rtrim(empty($directory) ? dirname($path) : $directory, '/') . '/';
		$this->manifest = $manifest;
		$this->toc = $toc;
    	$this->user = empty($user) ? User::get_data_manager()->retrieve_user(Session::get_user_id()) : $user;
		$this->log = empty($log) ? new Log() : $log;
		$this->cache = new ObjectCache();
    }
    
    /**
     * @return DataClass
     */
    public function get_object(){
    	return $this->object;
    }
    
    public function set_object($value){
    	$this->object = $value;
    }
    
    /**
     * @return ImscpManifestWriter
     */
    public function get_manifest(){
    	return $this->manifest;
    }
    
    public function set_manifest($value){
    	$this->manifest = $value;
    }
    
    /**
     * @return User
     */
    public function get_user(){
    	return $this->user;
    }
    
    public function set_user($value){
    	$this->user = $value;
    }
    
    /**
     * @return Log
     */
    public function get_log(){
    	return $this->log;
    }
    
    public function set_log($value){
    	$this->log = $value;
    }
    
 	/**
     * @return ObjectCache
     */
    public function get_cache(){
    	return $this->cache;
    }
    
    public function set_cache($value){
    	$this->cache = $value;
    }
    
    public function get_directory(){
    	return $this->directory;
    }
    
    public function set_directory($value){
    	$this->directory = empty($value) ? '' : rtrim($value, '/').'/';
    }
    
    public function get_toc(){
    	return $this->toc;
    }
    
    public function set_toc($value){
    	$this->toc = $value;
    }
    
    public function copy($object){
    	$result = clone $this;
    	$result->set_object($object);
    	return $result;
    }

}    
    



    
?>