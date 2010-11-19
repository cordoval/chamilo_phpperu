<?php
namespace common\libraries;

require_once dirname(__FILE__) . '/qti_resource_manager_base.class.php';

/**
 * Empty resource manager. Does nothing.
 *
 * @copyright (c) 2010 University of Geneva
 * @author laurent.opprecht@unige.ch
 *
 */
class QtiEmptyResourceManager extends QtiResourceManagerBase{

	static $_instance = null;

	public static function get_instance(){
		if(self::$_instance){
			return self::$_instance;
		}
		return self::$_instance = new self();
	}

	public function __construct(){
		parent::__construct('', '');
	}

    public function translate_path($path){
    	return $path;

    }

}




















?>