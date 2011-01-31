<?php 
namespace repository\content_object\survey;

use common\libraries\Path;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\DynamicTabsRenderer;
use common\libraries\Utilities;

require_once Path :: get_repository_content_object_path() . '/survey/php/manage/context/component/context_viewer.class.php';

class SurveyContextManagerUnsubscribeGroupComponent extends SurveyContextManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $context_registration_id = Request :: get(self :: PARAM_CONTEXT_REGISTRATION_ID);
    	$ids = Request :: get(self :: PARAM_CONTEXT_REL_GROUP_ID);
        
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                
                $context_rel_group_ids = explode('|', $id);
                
                $context_id = $context_rel_group_ids[0];
                
                if (SurveyContextManagerRights :: is_allowed_in_survey_context_manager_subtree(SurveyContextManagerRights :: SUBSCRIBE_GROUP_RIGHT, $context_id, SurveyContextManagerRights :: TYPE_CONTEXT_REGISTRATION))
                {
                    
                    $context_rel_group = SurveyContextDataManager :: get_instance()->retrieve_survey_context_rel_group($context_rel_group_ids[0], $context_rel_group_ids[1]);
                    
                    if (! $context_rel_group->delete())
                    {
                        $failures ++;
                    }
                    else
                    {
                        
                    //                    Event :: trigger('delete', 'context', array('target_context_id' => $context->get_id(), 'action_user_id' => $user->get_id()));
                    }
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'ObjectNotDeleted';
                }
                else
                {
                    $message = 'ObjectsNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'ObjectDeleted';
                }
                else
                {
                    $message = 'ObjectsDeleted';
                }
            }
            $this->redirect(Translation :: get($message, array('OBJECT' => Translation::get('SelectedSurveyContextRelGroup'), 'OBJECTS' => Translation::get('SelectedSurveyContextRelGroups')),Utilities::COMMON_LIBRARIES), ($failures ? true : false), array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT, self :: PARAM_CONTEXT_ID => $context_rel_group_ids[0],  self :: PARAM_CONTEXT_REGISTRATION_ID => $context_registration_id ,DynamicTabsRenderer :: PARAM_SELECTED_TAB => SurveyContextManagerContextViewerComponent :: TAB_GROUPS));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoSurveyContextRelGroupsSelected')));
        }
    }
}
?>