<?php

require_once dirname(__FILE__) . '/../survey_manager.class.php';
require_once dirname(__FILE__) . '/../survey_manager_component.class.php';
require_once dirname(__FILE__) . '/../../forms/survey_publication_form.class.php';

class SurveyManagerCreatorComponent extends SurveyManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $action = Request :: get(SurveyManager :: PARAM_ACTION);
        $testcase = false;
        if ($action === SurveyManager :: ACTION_TESTCASE)
        {
            $testcase = true;
        }
        
        $trail = new BreadcrumbTrail();
        if ($testcase)
        {
            $trail->add(new Breadcrumb($this->get_url(array(TestcaseManager :: PARAM_ACTION => TestcaseManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS)), Translation :: get('BrowseTestCaseSurveyPublications')));
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateTestCaseSurveyPublication')));
        }
        else
        {
            $trail->add(new Breadcrumb($this->get_url(array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS)), Translation :: get('BrowseSurveyPublications')));
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateSurveyPublication')));
        }
        
        $object_ids = Request :: get(RepoViewer :: PARAM_ID);
        $pub = new RepoViewer($this, array('survey'), true);
        
        $html = array();
        
        if (! isset($object_ids))
        {
            $html[] = $pub->as_html();
        }
        else
        {
            
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
            
            $form = new SurveyPublicationForm(SurveyPublicationForm :: TYPE_MULTI, $object_ids, $this->get_user(), $this->get_url($parameters));
            if ($form->validate())
            {
                $publication = $form->create_content_object_publications();
                
                if (! $publication)
                {
                    if ($testcase)
                    {
                    	$message = Translation :: get('TestCaseNotCreated');
                    }
                    else
                    {
                        $message = Translation :: get('SurveyNotPublished');
                    
                    }
                }
                else
                {
                    if ($testcase)
                    {
                    	$message = Translation :: get('TestCaseCreated');
                    }
                    else
                    {
                        $message = Translation :: get('SurveyPublished');
                    
                    }
                }
                
                if ($testcase)
                {
                    $this->redirect($message, (! $publication ? true : false), array(TestcaseManager :: PARAM_ACTION => TestcaseManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS));
                }
                else
                {
                    $this->redirect($message, (! $publication ? true : false), array(Application :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS));
                }
            
            }
            else
            {
                $html[] = $form->toHtml();
            }
        }
        
        $this->display_header($trail);
        echo implode("\n", $html);
        echo '<div style="clear: both;"></div>';
        $this->display_footer();
    }
}
?>