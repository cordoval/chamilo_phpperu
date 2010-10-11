<?php
require_once (Path :: get_repository_path().'/lib/content_object/survey/survey_context.class.php');
class SurveyContext8 extends SurveyContext
{
 const CLASS_NAME = __CLASS__;

 const PROPERTY_ZEZE = 'zeze';

static function get_additional_property_names()
{
return array(self :: PROPERTY_ZEZE);
}
function get_zeze()
{
  return $this->get_additional_property(self :: PROPERTY_ZEZE);
}

function set_zeze($zeze)
{
   $this->set_additional_property(self :: PROPERTY_ZEZE, $zeze);
}
static public function get_allowed_keys()
{
	 return array();
}
static function get_table_name()
{
  return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
}
}
?>