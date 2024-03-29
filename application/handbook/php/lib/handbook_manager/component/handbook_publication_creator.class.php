<?php
namespace application\handbook;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use user\UserDataManager;
use common\extensions\repo_viewer\RepoViewer;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\InCondition;
use common\libraries\Theme;
use repository\ContentObject;
use repository\content_object\handbook\Handbook;
use repository\RepositoryDataManager;

require_once dirname(__FILE__) . '/../handbook_manager.class.php';
require_once dirname(__FILE__) . '/../../forms/handbook_publication_form.class.php';

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
        $trail->add(new Breadcrumb($this->get_url(array(
                HandbookManager :: PARAM_ACTION => HandbookManager :: ACTION_BROWSE)), Translation :: get('Browse' , array('OBJECT' => Translation::get('HandbookPublication')), Utilities::COMMON_LIBRARIES)));

        $udm = UserDataManager :: get_instance();
        $user = $udm->retrieve_user($this->get_user_id());

        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Create' , array('OBJECT' => Translation::get('HandbookPublication')), Utilities::COMMON_LIBRARIES)));
        $trail->add_help('Handbook_Create');

        $html = array();
        if (! RepoViewer :: is_ready_to_be_published())
        {
            $repo_viewer = RepoViewer :: construct($this);
            $repo_viewer->run();
        }
        else
        {
            $object = RepoViewer :: get_selected_objects();

            if (! is_array($object))
            {
                $object = array(
                        $object);
            }

            $handbook_publication = new HandbookPublication();

            //            $form = new HandbookPublicationForm(HandbookPublicationForm :: TYPE_CREATE, $handbook_publication, $this->get_url(array(RepoViewer :: PARAM_ACTION => RepoViewer :: ACTION_PUBLISHER, RepoViewer :: PARAM_ID => $object)), $this->get_user(), HandbookRights :: TYPE_HANDBOOK_FOLDER);
            $form = new HandbookPublicationForm(HandbookPublicationForm :: TYPE_CREATE, $handbook_publication, $this->get_url(array(
                    RepoViewer :: PARAM_ACTION => RepoViewer :: ACTION_PUBLISHER,
                    RepoViewer :: PARAM_ID => $object)), $this->get_user());

            if ($form->validate())
            {
                $success = $form->create_handbook_publications($object);
                if($success !== false)
                {
                $this->redirect($success ? Translation :: get('ObjectCreated', array('OBJECT'=> Translation::get('Handbook')), Utilities::COMMON_LIBRARIES) : Translation :: get('ObjectNotCreated', array('OBJECT'=> Translation::get('Handbook')), Utilities::COMMON_LIBRARIES), ! $success, array(
                        HandbookManager :: PARAM_ACTION => HandbookManager :: ACTION_VIEW_HANDBOOK,
                        HandbookManager::PARAM_HANDBOOK_PUBLICATION_ID => $success,
                        HandbookManager::PARAM_TOP_HANDBOOK_ID =>$object[0],
                        HandbookManager :: PARAM_HANDBOOK_ID => $object[0]));
                }

            }
            else
            {
                $condition = new InCondition(ContentObject :: PROPERTY_ID, $object, ContentObject :: get_table_name());
                $content_objects = RepositoryDataManager :: get_instance()->retrieve_content_objects($condition);

                $html[] = '<div class="content_object padding_10">';
                $html[] = '<div class="title">' . Translation :: get('SelectedContentObjects' , array('OBJECT' => Translation::get('Handbooks')), Utilities::COMMON_LIBRARIES) . '</div>';
                $html[] = '<div class="description">';
                $html[] = '<ul class="attachments_list">';

                while ($content_object = $content_objects->next_result())
                {
                    $html[] = '<li><img src="' . Theme :: get_image_path(ContentObject :: get_content_object_type_namespace($content_object->get_type())) . 'logo/' . $content_object->get_icon_name(Theme :: ICON_MINI) . '.png" alt="' . htmlentities(Translation :: get(ContentObject :: type_to_class($content_object->get_type())), 'repository\\content_object\\') . '"/> ' . $content_object->get_title() . '</li>';
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