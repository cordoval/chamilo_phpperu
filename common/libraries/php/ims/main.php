<?php

/**
 * IMS
 * ---
 * Library to export/import to/from QTI and CP formats.
 * Used by the QTI and CP export/import modules.
 * 
 * See: 
 * 
 * 		CP: http://www.imsglobal.org/content/packaging
 * 		QTI: http://www.imsglobal.org/question
 * 
 * For further information.
 * 
 * Sub folders:
 * 
 * chamilo
 * -------
 * Chamilo related classes used by the export/import modules. Other folders do not have dependencies 
 * to Chamilo.
 *  
 * common
 * ------
 * IMS specific library shared by QTI and CP
 * 
 * cp
 * --
 * IMS CP related classes
 * 
 * lib
 * ---
 * Generic libraries used by this package. Do not contains anything specific to IMS.
 * 
 * qti
 * ---
 * IMS QTI related classes
 * 
 * 
 */

require_once dirname(__FILE__) .'/lib/debug_util.class.php';
require_once dirname(__FILE__) .'/lib/util.php';

require_once_all(dirname(__FILE__) .'/lib/*.class.php');

require_once_all(dirname(__FILE__) .'/common/*.class.php');
require_once_all(dirname(__FILE__) .'/common/reader/*.class.php');
require_once_all(dirname(__FILE__) .'/common/writer/*.class.php');

require_once_all(dirname(__FILE__) .'/qti/reader/*.class.php');
require_once_all(dirname(__FILE__) .'/qti/writer/*.class.php');

require_once_all(dirname(__FILE__) .'/qti/*.class.php');


require_once dirname(__FILE__) .'/qti/import_strategy/qti_import_strategy_base.class.php';
require_once_all(dirname(__FILE__) .'/qti/import_strategy/*.class.php');

require_once_all(dirname(__FILE__) .'/cp/*.class.php');
require_once_all(dirname(__FILE__) .'/cp/writer/*.class.php');
require_once_all(dirname(__FILE__) .'/cp/reader/*.class.php');

require_once_all(dirname(__FILE__) .'/chamilo/*.class.php');
require_once_all(dirname(__FILE__) .'/chamilo/import/*.class.php');
require_once_all(dirname(__FILE__) .'/chamilo/export/*.class.php');




















?>