<?php 
namespace repository\content_object\survey;

use common\libraries\Toolbar;
use common\libraries\Toolbaritem;
use common\libraries\Translation;
use common\libraries\Theme;


require_once dirname(__FILE__) . '/table_column_model.class.php';

class SurveyUserTableCellRenderer extends DefaultSurveyUserTableCellRenderer
{
    
    private $component;
    private $publication_id;
    private $type;

    function __construct($component)
    {
        $this->component = $component;
    }

    // Inherited
    function render_cell($column, $user)
    {
        if ($column === SurveyUserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($user);
        }
        
        return parent :: render_cell($column, $user);
    }

    private function get_modification_links($user)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
//        if ($this->type == SurveyUserTable :: TYPE_INVITEES)
//        {
//            $toolbar->add_item(new ToolbarItem(Translation :: get('TakeSurvey'), Theme :: get_common_image_path() . 'action_next.png', $this->component->get_survey_invitee_publication_viewer_url($this->publication_id, $user->get_id()), ToolbarItem :: DISPLAY_ICON));
//        }
//        
//        $toolbar->add_item(new ToolbarItem(Translation :: get('CancelInvitation'), Theme :: get_common_image_path() . 'action_unsubscribe.png', $this->component->get_survey_cancel_invitation_url($this->publication_id, $user->get_id()), ToolbarItem :: DISPLAY_ICON));
        
        return $toolbar->as_html();
    }

}
?>