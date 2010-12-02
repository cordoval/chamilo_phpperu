<?php
namespace repository\content_object\survey;

use common\libraries\Path;
require_once (Path :: get_repository_path().'/lib/content_object/survey/survey_template.class.php');


class SurveyTemplate3 extends SurveyTemplate
{
 const CLASS_NAME = __CLASS__;

 const PROPERTY_LEVEL_1_ID = 'level_1_id';
 const PROPERTY_LEVEL_2_STUDIEDUUR = 'level_2_studieduur';

static function get_additional_property_names($with_context_type = false)
{
 if ($with_context_type)
{
return array(self :: PROPERTY_LEVEL_1_ID => survey_context_5, self :: PROPERTY_LEVEL_2_STUDIEDUUR => survey_context_4);
 }
else
{
return array(self :: PROPERTY_LEVEL_1_ID, self :: PROPERTY_LEVEL_2_STUDIEDUUR);
}
}
function get_level_1_id()
{
  return $this->get_additional_property(self :: PROPERTY_LEVEL_1_ID);
}

function set_level_1_id($level_1_id)
{
   $this->set_additional_property(self :: PROPERTY_LEVEL_1_ID, $level_1_id);
}
function get_level_2_studieduur()
{
  return $this->get_additional_property(self :: PROPERTY_LEVEL_2_STUDIEDUUR);
}

function set_level_2_studieduur($level_2_studieduur)
{
   $this->set_additional_property(self :: PROPERTY_LEVEL_2_STUDIEDUUR, $level_2_studieduur);
}
static function get_table_name()
{
  return Utilities :: camelcase_to_underscores(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
}
}
?>