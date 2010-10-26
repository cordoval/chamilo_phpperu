<?php
namespace common\libraries;
/**
 * Main entry point to use the library.
 */

require_once(dirname(__FILE__) . '/fedora_fs_base.class.<?php
namespace common\libraries;');
require_once(dirname(__FILE__) . '/fedora_fs_datastream.class.<?php
namespace common\libraries;');
require_once(dirname(__FILE__) . '/fedora_fs_folder.class.<?php
namespace common\libraries;');
require_once(dirname(__FILE__) . '/fedora_fs_mystuff.class.<?php
namespace common\libraries;');
require_once(dirname(__FILE__) . '/fedora_fs_object.class.<?php
namespace common\libraries;');
require_once(dirname(__FILE__) . '/fedora_fs_store.class.<?php
namespace common\libraries;');
require_once(dirname(__FILE__) . '/fedora_fs_history.class.<?php
namespace common\libraries;');
require_once(dirname(__FILE__) . '/fedora_fs_search.class.<?php
namespace common\libraries;');
require_once(dirname(__FILE__) . '/fedora_fs_search_by_id.class.<?php
namespace common\libraries;');
require_once(dirname(__FILE__) . '/fedora_fs_lastobjects.class.<?php
namespace common\libraries;');
require_once(dirname(__FILE__) . '/fedora_fs_sparql_query.class.<?php
namespace common\libraries;');
require_once(dirname(__FILE__) . '/fedora_fs_itql_query.class.<?php
namespace common\libraries;');
require_once(dirname(__FILE__) . '/fedora_fs_subject.class.<?php
namespace common\libraries;');
require_once(dirname(__FILE__) . '/fedora_fs_access_right.class.<?php
namespace common\libraries;');

function fedora_fs_translate($key, $module){
	return Translation::get_instance()->Translate($key);
}

function fedora_fs_resource($file){
	return Path::get_home_path() .  '/common/fedora/resource/' . $file;
}

