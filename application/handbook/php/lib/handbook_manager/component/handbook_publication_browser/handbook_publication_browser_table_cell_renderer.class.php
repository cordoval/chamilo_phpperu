<?php
namespace application\handbook;
use common\libraries\Toolbar;
use user\User;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Theme;
use common\libraries\ToolbarItem;
use common\libraries\EqualityCondition;
use common\libraries\Application;
require_once   dirname(__FILE__) .  '/handbook_publication_browser_table_column_model.class.php';
require_once   dirname(__FILE__) . '/../../../tables/handbook_publication_table/default_handbook_publication_table_cell_renderer.class.php';

/**
 * Cell renderer for the handbook_publication object browser table
 */
class HandbookPublicationBrowserTableCellRenderer extends DefaultHandbookPublicationTableCellRenderer
{
    /**
     * The handbook_publication browser component
     */
    public $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function __construct($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $handbook)
    {
        if ($column === HandbookPublicationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($handbook);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            case User :: PROPERTY_OFFICIAL_CODE :
                return $handbook->get_id();
        }
        return parent :: render_cell($column, $handbook);
    }

    /**
     * Gets the action links to display
     * @param $handbook The handbook for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($handbook)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        $hdm = HandbookDataManager :: get_instance();
        $condition = new EqualityCondition(HandbookPublication :: PROPERTY_CONTENT_OBJECT_ID, $handbook->get_id());
        $publications = $hdm->retrieve_handbook_publications($condition);
        if (count($publications == 1))
        {
            $handbook_publication_id = $publications->next_result()->get_id();
        }


        $user_id = $this->browser->get_user_id();
//        $location_id = HandbookRights::get_location_id_by_identifier_from_handbooks_subtree($handbook_publication_id);
        $view_right = HandbookRights::is_allowed_in_handbooks_subtree(HandbookRights::VIEW_RIGHT, $handbook_publication_id, $user_id);
        $edit_right = HandbookRights::is_allowed_in_handbooks_subtree(HandbookRights::EDIT_RIGHT, $handbook_publication_id, $user_id);
        $delete_right = HandbookRights::is_allowed_in_handbooks_subtree(HandbookRights::DELETE_PUBLICATION_RIGHT, $handbook_publication_id, $user_id);



        if($view_right)
        {
            $toolbar->add_item(new ToolbarItem(
                            Translation :: get('View'),
                            Theme :: get_common_image_path() . 'action_browser.png',
                            $this->browser->get_view_handbook_publication_url($handbook->get_id(), $handbook_publication_id),
                            ToolbarItem :: DISPLAY_ICON
            ));
        }
        if($delete_right)
        {
            $toolbar->add_item(new ToolbarItem(
                            Translation :: get('Delete' ),
                            Theme :: get_common_image_path() . 'action_delete.png',
                            $this->browser->get_delete_handbook_publication_url($handbook_publication_id),
                            ToolbarItem :: DISPLAY_ICON
            ));
        }
        if($edit_right)
        {
            //handbook preferences
            //handbook rights
            $toolbar->add_item(new ToolbarItem(Translation :: get('EditPublicationRights'),
                    Theme :: get_common_image_path() . 'action_create.png',
                    $this->browser->get_url(array(Application::PARAM_APPLICATION => HandbookManager::APPLICATION_NAME, HandbookManager :: PARAM_ACTION => HandbookManager :: ACTION_EDIT_RIGHTS, HandbookManager:: PARAM_HANDBOOK_PUBLICATION_ID => $handbook_publication_id)),
                    ToolbarItem :: DISPLAY_ICON
            ));
        }

        return $toolbar->as_html();
    }
}
?>