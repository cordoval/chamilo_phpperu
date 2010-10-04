<?php
require_once (Path :: get_repository_path().'/lib/content_object/survey/survey_template.class.php');
class SurveyTemplate3 extends SurveyTemplate
{
 const CLASS_NAME = __CLASS__;

 const PROPERTY_LEVEL_1_USER = 'level_1_user';
 const PROPERTY_LEVEL_2_NAME = 'level_2_name';
 const PROPERTY_LEVEL_3_TEST = 'level_3_test';

static function get_additional_property_names($with_context_type = false)
{
 if ($with_context_type)
{
return array(self :: PROPERTY_LEVEL_1_USER => survey_context1, self :: PROPERTY_LEVEL_2_NAME => survey_context2, self :: PROPERTY_LEVEL_3_TEST => survey_context4);
 }
else
{
return array(self :: PROPERTY_LEVEL_1_USER, self :: PROPERTY_LEVEL_2_NAME, self :: PROPERTY_LEVEL_3_TEST);
}
}
function get_level_1_user()
{
  return $this->get_additional_property(self :: PROPERTY_LEVEL_1_USER);
}

function set_level_1_user($level_1_user)
{
   $this->set_additional_property(self :: PROPERTY_LEVEL_1_USER, $level_1_user);
}
function get_level_2_name()
{
  return $this->get_additional_property(self :: PROPERTY_LEVEL_2_NAME);
}

function set_level_2_name($level_2_name)
{
   $this->set_additional_property(self :: PROPERTY_LEVEL_2_NAME, $level_2_name);
}
function get_level_3_test()
{
  return $this->get_additional_property(self :: PROPERTY_LEVEL_3_TEST);
}

function set_level_3_test($level_3_test)
{
   $this->set_additional_property(self :: PROPERTY_LEVEL_3_TEST, $level_3_test);
}
static function get_table_name()
{
  return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
}
}
?>