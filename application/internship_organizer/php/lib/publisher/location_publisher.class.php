<?php
namespace application\internship_organizer;

use common\libraries\WebApplication;
use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\DynamicTabsRenderer;
use common\libraries\InCondition;

use common\extensions\repo_viewer\RepoViewer;

use repository\RepositoryDataManager;
use repository\ContentObject;

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'forms/location_publication_form.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'organisation_manager/component/viewer.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'organisation_manager/component/location_viewer.class.php';

class InternshipOrganizerLocationPublisher
{
    
    const SINGLE_LOCATION_TYPE = 'single';
    const MULTIPLE_LOCATION_TYPE = 'multiple';
    
    private $parent;
    private $type;

    function __construct($parent, $type)
    {
        $this->parent = $parent;
        $this->type = $type;
    }

    function get_publications_form($ids)
    {
        if (is_null($ids))
            return '';
        
        if (! is_array($ids))
        {
            $ids = array($ids);
        }
        
        $html = array();
        
        if (count($ids) > 0)
        {
            $condition = new InCondition(ContentObject :: PROPERTY_ID, $ids, ContentObject :: get_table_name());
            $content_objects = RepositoryDataManager :: get_instance()->retrieve_content_objects($condition);
            
            $html[] = '<div class="content_object padding_10">';
            $html[] = '<div class="title">' . Translation :: get('SelectedContentObjects') . '</div>';
            $html[] = '<div class="description">';
            $html[] = '<ul class="attachments_list">';
            
            while ($content_object = $content_objects->next_result())
            {
                $namespace =ContentObject :: get_content_object_type_namespace($content_object->get_type());
                $html[] = '<li><img src="' . Theme :: get_image_path($namespace) . 'logo/' . Theme :: ICON_MINI . '.png" alt="' . htmlentities(Translation :: get('TypeName', null, $namespace)) . '"/> ' . $content_object->get_title() . '</li>';
            }
            
            $html[] = '</ul>';
            $html[] = '</div>';
            $html[] = '</div>';
        }
        
        $parameters = $this->parent->get_parameters();
        $parameters[RepoViewer :: PARAM_ID] = $ids;
        $parameters[RepoViewer :: PARAM_ACTION] = RepoViewer :: ACTION_PUBLISHER;
        
        $form = new InternshipOrganizerLocationPublicationForm(InternshipOrganizerLocationPublicationForm :: TYPE_MULTI, $ids, $this->parent->get_user(), $this->parent->get_url($parameters), $this->type);
        if ($form->validate())
        {
            $publication = $form->create_content_object_publications();
            
            if (! $publication)
            {
                $message = Translation :: get('ObjectNotPublished');
            }
            else
            {
                $message = Translation :: get('ObjectPublished');
            }
            
            if ($this->type == self :: SINGLE_LOCATION_TYPE)
            {
                $location_id = $this->parent->get_parameter(InternshipOrganizerOrganisationManager :: PARAM_LOCATION_ID);
                $this->parent->redirect($message, (! $publication ? true : false), array(InternshipOrganizerOrganisationManager :: PARAM_ACTION => InternshipOrganizerOrganisationManager :: ACTION_VIEW_LOCATION, InternshipOrganizerOrganisationManager :: PARAM_LOCATION_ID => $location_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerOrganisationManagerLocationViewerComponent :: TAB_PUBLICATIONS));
            }
            
            if ($this->type == self :: MULTIPLE_LOCATION_TYPE)
            {
                $organisation_id = $this->parent->get_parameter(InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID);
                $this->parent->redirect($message, (! $publication ? true : false), array(InternshipOrganizerOrganisationManager :: PARAM_ACTION => InternshipOrganizerOrganisationManager :: ACTION_VIEW_ORGANISATION, InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID => $organisation_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerOrganisationManagerViewerComponent :: TAB_PUBLICATIONS));
            }
        
        }
        else
        {
            $html[] = $form->toHtml();
            $html[] = '<div style="clear: both;"></div>';
            
            $this->parent->display_header();
            echo implode("\n", $html);
            $this->parent->display_footer();
        }
    }
}
?>