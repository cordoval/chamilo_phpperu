<?php
require_once (Path :: get_repository_path()\.'lib/content_object/survey/survey_context.class.php');
class SurveyRtesrtsertContext extends SurveyContext
{
 const CLASS_NAME = __CLASS__;

 const PROPERTY_TFSDF T = 'tfsdf t';

static function get_additional_property_names()
{
return array(self :: PROPERTY_TFSDF T);
}
function get_tfsdf t()
{
  return $this->get_additional_property(self :: PROPERTY_TFSDF T);
}

function set_tfsdf t($tfsdf t)
{
   $this->set_additional_property(self :: PROPERTY_TFSDF T, $tfsdf t);
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