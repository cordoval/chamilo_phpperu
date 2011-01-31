<?php
namespace application\survey;

use repository\content_object\survey\SurveyTemplateUser;

use common\libraries\InCondition;

use repository\content_object\survey\SurveyTemplate;

use common\libraries\EqualityCondition;

use repository\content_object\survey\SurveyContextDataManager;

use common\libraries\Utilities;
use common\libraries\Path;
use common\libraries\Translation;
use common\libraries\FormValidator;
use user\UserDataManager;
use rights\RightsUtilities;
use rights\RightsDataManager;

class SurveySubscribeTemplateUserForm extends FormValidator
{
    
    const APPLICATION_NAME = 'survey';
    const PARAM_TEMPLATE_IDS = 'template_ids';
    
    private $parent;
    private $publication;
    private $user;

    function __construct($publication, $action, $user)
    {
        parent :: __construct('subscribe_users', 'post', $action);
        
        $this->publication = $publication;
        $this->user = $user;
        $this->build_form();
        $this->setDefaults();
    }

    function build_form()
    {
        $publication = $this->publication;
        
        $this->addElement('multiselect', self :: PARAM_TEMPLATE_IDS, Translation :: get('Templates'), $this->get_templates());
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('SubscribeTemplateUsers'), array(
                'class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array(
                'class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $this->addElement('category');
        $this->addElement('html', '<br />');
    
    }

    private function get_templates()
    {
        $publication = $this->publication;
        
        $survey = $publication->get_publication_object();
        $contex_template = $survey->get_context_template_for_level();
      
        $condition = new EqualityCondition(SurveyTemplate :: PROPERTY_CONTEXT_TEMPLATE_ID, $contex_template->get_id());
        $templates = SurveyContextDataManager :: get_instance()->retrieve_survey_templates($condition);
        $temps = array();
        while ($template = $templates->next_result())
        {
            $temps[$template->get_id()] = $template->get_name();
        }
        return $temps;
    }

    function create_user_rights()
    {
        $publication_id = $this->publication->get_id();
        
        $values = $this->exportValues();
        
        $succes = false;
        
        $location_id = SurveyRights :: get_location_id_by_identifier_from_surveys_subtree($publication_id, SurveyRights :: TYPE_PUBLICATION);
        
        $publication = $this->publication;
        
        $survey = $publication->get_publication_object();
        $contex_template = $survey->get_context_template_for_level();
        $type = $contex_template->get_type();
        $template_ids = $values[self :: PARAM_TEMPLATE_IDS];
        
        $condition = new InCondition(SurveyTemplateUser::PROPERTY_TEMPLATE_ID, $template_ids, SurveyTemplateUser :: get_table_name());
        
        $template_users = SurveyContextDataManager::get_instance()->retrieve_survey_template_users($type, $condition );
        
        $user_ids = array();
        
        while($template_user = $template_users->next_result()){
        	$user_ids[] = $template_user->get_user_id();
        }
        
        
        if (count($user_ids))
        {
            foreach ($user_ids as $user_id)
            {
                $succes = RightsUtilities :: set_user_right_location_value(SurveyRights::RIGHT_PARTICIPATE, $user_id, $location_id, 1);
            }
        }
        
        return $succes;
    }

}

?>