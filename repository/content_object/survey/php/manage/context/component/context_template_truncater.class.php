<?php
namespace repository\content_object\survey;

use common\libraries\Translation;

use common\libraries\Request;
use common\libraries\Utilities;

class SurveyContextManagerContextTemplateTruncaterComponent extends SurveyContextManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(SurveyBuilder :: PARAM_TEMPLATE_ID);
        $failures = 0;

        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            $dm = SurveyContextDataManager :: get_instance();

            foreach ($ids as $id)
            {
                $survey_id = $this->get_root_content_object_id();

                if (! $dm->truncate_survey_context_template($survey_id, $id))
                {
                    $failures ++;
                }

     //                else
            //                {
            //                    Event :: trigger('empty', 'category', array('target_category_id' => $category->get_id(), 'action_user_id' => $user->get_id()));
            //                }
            }

            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'ObjectNotEmptied';
                }
                else
                {
                    $message = 'ObjectsNotEmptied';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'ObjectEmptied';
                }
                else
                {
                    $message = 'ObjectsEmptied';
                }

            }

            if (count($ids) == 1)
            {
                $this->redirect(Translation :: get($message, array('OBJECT' => Translation::get('SelectedContextTemplate'), 'OBJECTS' => Translation::get('SelectedContextTemplates')),Utilities::COMMON_LIBRARIES), ($failures ? true : false), array(
                        SurveyBuilder :: PARAM_BUILDER_ACTION => SurveyBuilder :: ACTION_VIEW_CONTEXT,
                        SurveyBuilder :: PARAM_TEMPLATE_ID => $ids[0]));
            }
            else
            {
                $this->redirect(Translation :: get($message), ($failures ? true : false), array(
                        SurveyBuilder :: PARAM_BUILDER_ACTION => SurveyBuilder :: ACTION_BROWSE_CONTEXT));

            }

        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoContextTemplatesSelected')));
        }
    }
}
?>