<?php




namespace application\portfolio;
use common\extensions\repo_viewer\RepoViewerInterface;
use common\libraries\Breadcrumb;
use user\UserDataManager;
use common\libraries\BreadcrumbTrail;
use common\libraries\Translation;
use common\extensions\repo_viewer\RepoViewer;
use common\libraries\InCondition;
use common\libraries\Theme;
use repository\ContentObject;
use repository\content_object\portfolio\Portfolio;
use repository\RepositoryDataManager;
require_once dirname(__FILE__) . '/../../forms/portfolio_publication_form.class.php';
require_once dirname(__FILE__) . '/../../../rights/portfolio_rights.class.php';

/**
 * Component to create a new portfolio_publication object
 * @author Sven Vanpoucke
 */
class PortfolioManagerPortfolioPublicationCreatorComponent extends PortfolioManager implements RepoViewerInterface
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_BROWSE)), Translation :: get('BrowsePortfolio')));

        $udm = UserDataManager :: get_instance();
        $user = $udm->retrieve_user($this->get_user_id());
        $trail->add(new Breadcrumb($this->get_url(array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO, PortfolioManager :: PARAM_PORTFOLIO_OWNER_ID => $this->get_user_id())), Translation :: get('ViewPortfolio') . ' ' . $user->get_fullname()));

        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreatePortfolio')));
        $trail->add_help('portfolio create');

        
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

            $portfolio_publication = new PortfolioPublication();

            $form = new PortfolioPublicationForm(PortfolioPublicationForm :: TYPE_CREATE, $portfolio_publication, $this->get_url(array(RepoViewer :: PARAM_ACTION => RepoViewer :: ACTION_PUBLISHER, RepoViewer :: PARAM_ID => $object)), $this->get_user(), PortfolioRights :: TYPE_PORTFOLIO_FOLDER);

            if ($form->validate())
            {
                $success = $form->create_portfolio_publications($object);
                $this->redirect($success ? Translation :: get('PortfolioCreated') : Translation :: get('PortfolioNotCreated'), ! $success, array(
                        PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO, PortfolioManager :: PARAM_PORTFOLIO_OWNER_ID => $this->get_user_id()));
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
        return array(Portfolio :: get_type_name());
    }
}
?>