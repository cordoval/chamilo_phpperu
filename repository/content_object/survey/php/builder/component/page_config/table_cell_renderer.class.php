<?php
namespace repository\content_object\survey;

use common\libraries\DatetimeUtilities;

use repository\ContentObject;
use common\libraries\ToolbarItem;
use common\libraries\Toolbar;
use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\Utilities;
use repository\content_object\survey_page\SurveyPage;

class SurveyPageConfigTableCellRenderer
{
    
    private $browser;

    function __construct($browser)
    {
        $this->browser = $browser;
    }

    function render_cell($property, $config)
    {
        
        switch ($property)
        {
            case SurveyPage :: ANSWERMATCHES :
                $answers = array();
                foreach ($config[$property] as $key => $value)
                {
                    $answers[] = $key . '=>' . $value;
                }
                return implode(', ', $answers);
                break;
            case SurveyPage :: TO_VISIBLE_QUESTIONS_IDS :
                return implode(', ', $config[$property]);
                break;
            case SurveyPage :: CONFIG_CREATED :
                return DatetimeUtilities :: format_locale_date(null, $config[$property]);
                break;
            case SurveyPage :: CONFIG_UPDATED :
                return DatetimeUtilities :: format_locale_date(null, $config[$property]);
                break;
            default :
                ;
                break;
        }
        return $config[$property];
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    function get_modification_links($config)
    {
        
        $toolbar = new Toolbar();
        $toolbar->add_item(new ToolbarItem(Translation :: get('Edit', array('OBJECT' => Translation :: get('SurveyPageConfig')), Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_config_update_url($config[SurveyPage :: CONFIG_CREATED]), ToolbarItem :: DISPLAY_ICON));
        
        $toolbar->add_item(new ToolbarItem(Translation :: get('Delete', array('OBJECT' => Translation :: get('SurveyPageConfig')), Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_config_delete_url($config[SurveyPage :: CONFIG_CREATED]), ToolbarItem :: DISPLAY_ICON));
        return $toolbar->as_html();
    
    }

    function render_id_cell($complex_item)
    {
        $id = $complex_item->get_id();
        return $id;
    }

    function get_properties()
    {
        return array(SurveyPage :: CONFIG_NAME => 'name', SurveyPage :: FROM_VISIBLE_QUESTION_ID => 'fromquestion', SurveyPage :: TO_VISIBLE_QUESTIONS_IDS => 'toquestions', SurveyPage :: ANSWERMATCHES => 'answermatches', SurveyPage :: CONFIG_CREATED => 'created', SurveyPage :: CONFIG_UPDATED => 'updated');
    }

    function get_property_count()
    {
        return 6;
    }

}
?>