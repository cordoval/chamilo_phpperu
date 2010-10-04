<?php

/**
 * Store import settings. File path, category, log, etc.
 *
 *
 * @copyright (c) 2010 University of Geneva
 *
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class ObjectImportSettings
{
	private $path = '';
	private $filename = '';
	private $extention = '';
	private $metadata = null;
	private $category_id = 0;
	private $user = NULL;
	private $log = NULL;
	private $directory = '';
	private $type = '';
	//private $cache = NULL;
	private $level = 0;

	public function __construct($path='', $filename= '', $extention = '', $user=NULL, $category_id=0, Log $log=NULL, $metadata=NULL){
		$this->path = $path;
		$this->filename = $filename;
		$this->extention = $extention;
		$this->user = empty($user) ? User::get_data_manager()->retrieve_user(Session::get_user_id()) : $user;
		$this->category_id = $category_id;
		$this->log = empty($log) ? new Log() : $log;
		$this->metadata = $metadata;
		$this->directory = rtrim(empty($directory) ? dirname($path) : $directory, '/') . '/';
		$this->cache = new ObjectCache();
	}

	public function get_path(){
		return $this->path;
	}

	public function get_extention(){
		if($this->extention){
			$result = $this->extention;
		}else{
			$path = $this->get_path();
			$parts = explode('.', $path);
			$result = end($parts);
		}
		return $result;
	}

	public function get_filename(){
		return $this->filename ? $this->filename : basname($this>get_path());
	}

	public function get_directory(){
		$result = $this->directory ? $this->directory : dirname($this->get_path());
		$result = rtrim($result, '/').'/';
		return $result;
	}

	public function get_user(){
		return $this->user;
	}

	public function get_category_id(){
		return $this->category_id;
	}

	/**
	 * @return Log
	 */
	public function get_log(){
		return $this->log;
	}

	/**
	 * @return ObjectCache
	 */
	public function get_cache(){
		return $this->cache;
	}

	public function get_metadata(){
		return $this->get_metadata;
	}

	public function get_type(){
		return $this->type;
	}

	/**
	 * Import level. Starts at 0, increment by one each time settings are copied.
	 * Children of initial file have level 1. Grandchildren level 2, etc.
	 */
	public function get_level(){
		return $this->level;
	}

	public function copy($path, $filename='', $extention='', $type = ''){
		$result = clone $this;
		$result->path = $path;
		$result->filename = $filename;
		$result->directory = '';
		$result->extention = $extention;
		$result->type = $type;
		$result->metadata = $metadata;
		$result->level++;
		return $result;
	}

	/**
	 * Clear cached results when cloning to ensure the cache is synchronized with new values.
	 */
	public function __clone() {
		$this->reader = NULL;
		$this->manifest_reader = NULL;
		$this->dom = NULL;
	}

	private $reader = NULL;
	/**
	 * Return an XML reader for the file pointed to. Function is memoized.
	 * @return ImsXmlReader
	 */
	public function get_reader(){
		if(!empty($this->reader)){
			return $this->reader;
		}

		$path = $this->path;
		$ext = pathinfo($path, PATHINFO_EXTENSION);
		if($ext == 'xml'){
			return $this->reader = new ImsXmlReader($path);
		}else{
			return $this->reader = ImsXmlReader::get_empty_reader();
		}
	}

	private $manifest_reader = NULL;
	/**
	 * Returns a manifest reader for the file pointed to or NULL. Function is memoized.
	 *
	 * @return ImsXmlReader
	 */
	public function get_manifest_reader(){
		if(empty($this->manifest_reader)){
			$path = $this->get_path();
			if(is_dir($path)){
				$dir = $path;
				$files = scandir($path);
				$files = array_diff($files, array('.', '..'));
				foreach($files as $file){
					if(strtolower($file) == 'imsmanifest.xml'){
						$path = $dir .'/'. $file;
						return $this->manifest_reader = new ImscpManifestReader($dir .'/'. $file);
					}
				}
			}
			return $this->manifest_reader = ImsXmlReader::get_empty_reader();
		}
		return $this->manifest_reader;
	}

	private $dom = NULL;
	/**
	 * @return DOMDocument
	 */
	public function get_dom(){
		if($this->dom){
			return $this->dom;
		}
		
		$doc = new DOMDocument();
		if($doc->loadHTMLFile($this->get_path())){
			return $this->dom = $doc;
		}else{
			return NULL;
		}
	}

}





?>