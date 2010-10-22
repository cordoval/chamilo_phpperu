<?php 
namespace application\survey;

use common\extensions\repo_viewer\RepoViewerInterface;
use common\extensions\repo_viewer\RepoViewer;
use repository\content_object\survey\Survey;

//require_once dirname(__FILE__) . '/../survey_manager.class.php';
//require_once dirname(__FILE__) . '/../../forms/survey_publication_form.class.php';

class SurveyManagerPublisherComponent extends SurveyManager implements RepoViewerInterface
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        if (! SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_PUBLISH, SurveyRights :: LOCATION_BROWSER, SurveyRights :: TYPE_COMPONENT))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $html = array();
        
        if (! RepoViewer :: is_ready_to_be_published())
        {
            $repo_viewer = RepoViewer :: construct($this);
            $repo_viewer->run();
        }
        else
        {
            $object_ids = RepoViewer :: get_selected_objects();
            if (! is_array($object_ids))
            {
                $object_ids = array($object_ids);
            }
            
            if (count($object_ids) > 0)
            {
                $condition = new InCondition(ContentObject :: PROPERTY_ID, $object_ids, ContentObject :: get_table_name());
                $content_objects = RepositoryDataManager :: get_instance()->retrieve_content_objects($condition);
                
                $html[] = '<div class="content_object padding_10">';
                $html[] = '<div class="title">' . Translation :: get('SelectedContentObjects') . '</div>';
                $html[] = '<div class="description">';
                $html[] = '<ul class="attachments_list">';
                
                while ($content_object = $content_objects->next_result())
                {
                    $html[] = '<li><img src="' . Theme :: get_common_image_path() . 'treemenu_types/' . $content_object->get_type() . '.png" alt="' . htmlentities(Translation :: get(ContentObject :: type_to_class($content_object->get_type()) . 'TypeName')) . '"/> ' . $content_object->get_title() . '</li>';
                }
                
                $html[] = '</ul>';
                $html[] = '</div>';
                $html[] = '</div>';
            }
            
            $parameters = $this->get_parameters();
            $parameters[RepoViewer :: PARAM_ID] = $object_ids;
            $parameters[RepoViewer :: PARAM_ACTION] = RepoViewer :: ACTION_PUBLISHER;
            
            $form = new SurveyPublicationForm(SurveyPublicationForm :: TYPE_CREATE, $object_ids, $this->get_user(), $this->get_url($parameters));
            if ($form->validate())
            {
                $succes = $form->create_publications();
                
                if (! $succes)
                {
                    $message = Translation :: get('SurveyNotPublished');
                }
                else
                {
                    $message = Translation :: get('SurveyPublished');
                    $tab = $form->get_publication_type();
                }
                
                $this->redirect($message, (! $succes ? true : false), array(Application :: PARAM_ACTION => self :: ACTION_BROWSE, DynamicTabsRenderer :: PARAM_SELECTED_TAB => $tab));
            
            }
            else
            
            {
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
        return array(Survey :: get_type_name());
    }
}
?>