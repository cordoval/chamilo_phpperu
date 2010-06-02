<?php

require_once dirname(__FILE__) .'/lib/debug_util.class.php';
require_once dirname(__FILE__) .'/lib/util.php';
require_once_all(dirname(__FILE__) .'/lib/*.class.php');

require_once dirname(__FILE__) .'/qti/reader/ims_xml_reader.class.php';
require_once_all(dirname(__FILE__) .'/qti/reader/*.class.php');

require_once dirname(__FILE__) .'/qti/writer/Ims_id_factory.class.php';
require_once dirname(__FILE__) .'/qti/writer/Ims_xml_writer.class.php';
require_once_all(dirname(__FILE__) .'/qti/writer/*.class.php');

require_once dirname(__FILE__) .'/qti/qti_resource_manager_base.class.php';
require_once dirname(__FILE__) .'/qti/qti_renderer_base.class.php';
require_once_all(dirname(__FILE__) .'/qti/*.class.php');

require_once dirname(__FILE__) .'/qti/import_strategy/qti_import_strategy_base.class.php';
require_once_all(dirname(__FILE__) .'/qti/import_strategy/*.class.php');

require_once_all(dirname(__FILE__) .'/chamilo/*.class.php');

require_once dirname(__FILE__) . '/chamilo/object_import/builder_base.class.php';
require_once_all(dirname(__FILE__) . '/chamilo/object_import/*.class.php');

require_once PATH :: get_repository_path() . 'lib/content_object/assessment_multiple_choice_question/assessment_multiple_choice_question_option.class.php';
require_once PATH :: get_repository_path() . 'lib/content_object/assessment_matching_question/assessment_matching_question_option.class.php';
