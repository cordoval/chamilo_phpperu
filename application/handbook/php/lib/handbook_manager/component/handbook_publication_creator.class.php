<?php
/**
 * @package application.handbook.handbook.component
 */
require_once dirname(__FILE__).'/../handbook_manager.class.php';
require_once dirname(__FILE__).'/../../forms/handbook_publication_form.class.php';

/**
 * Component to create a new handbook_publication object
 * @author Sven Vanpoucke
 * @author Nathalie Blocry
 */
class HandbookManagerHandbookPublicationCreatorComponent extends HandbookManager
{
	/**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(HandbookManager :: PARAM_ACTION => HandbookManager :: ACTION_BROWSE)), Translation :: get('BrowseHandbook')));

        $udm = UserDataManager :: get_instance();
        $user = $udm->retrieve_user($this->get_user_id());
        
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateHandbook')));
        $trail->add_help('Handbook_Create');


        $html = array();
        if (!RepoViewer::is_ready_to_be_published())
        {
            $repo_viewer = RepoViewer :: construct($this);
            $repo_viewer->run();
        }
        else
        {
            $object = RepoViewer::get_selected_objects();

            if (! is_array($object))
            {
                $object = array($object);
            }

            $handbook_publication = new HandbookPublication();

//            $form = new HandbookPublicationForm(HandbookPublicationForm :: TYPE_CREATE, $handbook_publication, $this->get_url(array(RepoViewer :: PARAM_ACTION => RepoViewer :: ACTION_PUBLISHER, RepoViewer :: PARAM_ID => $object)), $this->get_user(), HandbookRights :: TYPE_HANDBOOK_FOLDER);
            $form = new HandbookPublicationForm(HandbookPublicationForm :: TYPE_CREATE, $handbook_publication, $this->get_url(array(RepoViewer :: PARAM_ACTION => RepoViewer :: ACTION_PUBLISHER, RepoViewer :: PARAM_ID => $object)), $this->get_user());
           

            if ($form->validate())
            {
                $success = $form->create_handbook_publications($object);
                $this->redirect($success ? Translation :: get('HandbookCreated') : Translation :: get('HandbookNotCreated'), ! $success, array(
                        HandbookManager :: PARAM_ACTION => HandbookManager :: ACTION_VIEW_HANDBOOK, HandbookManager :: PARAM_HANDBOOK_OWNER_ID => $this->get_user_id()));
            }
            else
            {
                $condition = new InCondition(ContentObject :: PROPERTY_ID, $object, ContentObject :: get_table_name());
                $content_objects = RepositoryDataManager :: get_instance()->retrieve_content_objects($condition);

                $html[] = '<div class="content_object padding_10">';
                $html[] = '<div class="title">' . Translation :: get('SelectedContentObjects') . '</div>';
                $html[] = '<div class="description">';
                $html[] = '<ul class="attachments_list">';

                while ($content_object = $content_objects->next_result())
                {
                    $html[] = '<li><img src="' . Theme :: get_common_image_path() . 'treemenu_types/' . $content_object->get_icon_name() . '.png" alt="' . htmlentities(Translation :: get(ContentObject :: type_to_class($content_object->get_type()) . 'TypeName')) . '"/> ' . $content_object->get_title() . '</li>';
                }

                $html[] = '</ul>';
                $html[] = '</div>';
                $html[] = '</div>';
                $html[] = $form->toHtml();
                $html[] = '<div style="clear: both;"></div>';

                $this->display_header();
                echo implode("\n", $html);
                $this->display_footer();
            }
        }
    }

    function get_allowed_content_object_types()
    {
        return array(Handbook :: get_type_name());
    }
}
?>