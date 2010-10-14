<?php
/**
 * Main entry point to use the library.
 */

require_once(dirname(__FILE__) . '/fedora_fs_base.class.php');
require_once(dirname(__FILE__) . '/fedora_fs_datastream.class.php');
require_once(dirname(__FILE__) . '/fedora_fs_folder.class.php');
require_once(dirname(__FILE__) . '/fedora_fs_mystuff.class.php');
require_once(dirname(__FILE__) . '/fedora_fs_object.class.php');
require_once(dirname(__FILE__) . '/fedora_fs_store.class.php');
require_once(dirname(__FILE__) . '/fedora_fs_history.class.php');
require_once(dirname(__FILE__) . '/fedora_fs_search.class.php');
require_once(dirname(__FILE__) . '/fedora_fs_search_by_id.class.php');
require_once(dirname(__FILE__) . '/fedora_fs_lastobjects.class.php');
require_once(dirname(__FILE__) . '/fedora_fs_sparql_query.class.php');
require_once(dirname(__FILE__) . '/fedora_fs_itql_query.class.php');
require_once(dirname(__FILE__) . '/fedora_fs_subject.class.php');
require_once(dirname(__FILE__) . '/fedora_fs_access_right.class.php');

function fedora_fs_translate($key, $module){
	return Translation::get_instance()->Translate($key);
}

function fedora_fs_resource($file){
	return Path::get_home_path() .  '/common/fedora/resource/' . $file;
}

