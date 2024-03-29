<?php
namespace application\internship_organizer;

use common\libraries\WebApplication;
use common\libraries\Translation;
use common\libraries\DynamicTabsRenderer;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Request;

use common\extensions\repo_viewer\RepoViewer;
use common\extensions\repo_viewer\RepoViewerInterface;

use repository\content_object\document\Document;
use repository\content_object\survey\Survey;

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'publisher/agreement_publisher.class.php';

class InternshipOrganizerAgreementManagerPublisherComponent extends InternshipOrganizerAgreementManager implements RepoViewerInterface
{

    private $type;
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $agreement_id = $_GET[self :: PARAM_AGREEMENT_ID];
        
        if ($agreement_id)
        {
            if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_PUBLISH, $agreement_id, InternshipOrganizerRights :: TYPE_AGREEMENT))
            {
                $this->display_header();
                $this->display_error_message(Translation :: get('NotAllowed'));
                $this->display_footer();
                exit();
            }
            $this->type = InternshipOrganizerAgreementPublisher :: SINGLE_AGREEMENT_TYPE;
            $this->set_parameter(self :: PARAM_AGREEMENT_ID, $agreement_id);
        }
        else
        {
            if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_PUBLISH, InternshipOrganizerRights :: LOCATION_AGREEMENT, InternshipOrganizerRights :: TYPE_COMPONENT))
            {
                $this->display_header();
                $this->display_error_message(Translation :: get('NotAllowed'));
                $this->display_footer();
                exit();
            }
            $this->type = InternshipOrganizerAgreementPublisher :: MULTIPLE_AGREEMENT_TYPE;
        }
              
        if (! RepoViewer :: is_ready_to_be_published())
        {
            $repo_viewer = RepoViewer :: construct($this);
            $repo_viewer->run();
        }
        else
        {
            $publisher = new InternshipOrganizerAgreementPublisher($this, $this->type);
            $publisher->get_publications_form(RepoViewer :: get_selected_objects());
        }
    }

    function get_allowed_content_object_types()
    {
        return array(Document :: get_type_name(), Survey :: get_type_name());
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_AGREEMENT)), Translation :: get('BrowseInternshipOrganizerAgreements')));
    	$agreement_id = Request :: get(self :: PARAM_AGREEMENT_ID);
        if($agreement_id){
    		$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_AGREEMENT, self :: PARAM_AGREEMENT_ID => $agreement_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerAgreementManagerViewerComponent :: TAB_PUBLICATIONS)), Translation :: get('ViewInternshipOrganizerAgreement')));
    	}   
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_AGREEMENT_ID);
    }

}
?>