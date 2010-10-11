<?php
require_once (Path :: get_repository_path()\.'lib/content_object/survey/survey_context.class.php');
class SurveyContext surveyContext extends SurveyContext
{
 const CLASS_NAME = __CLASS__;

 const PROPERTY_SURVEY = 'survey';

static function get_additional_property_names()
{
return array(self :: PROPERTY_SURVEY);
}
function get_survey()
{
  return $this->get_additional_property(self :: PROPERTY_SURVEY);
}

function set_survey($survey)
{
   $this->set_additional_property(self :: PROPERTY_SURVEY, $survey);
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