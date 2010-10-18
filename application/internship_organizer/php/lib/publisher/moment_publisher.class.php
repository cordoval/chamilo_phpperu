<?php

require_once dirname(__FILE__) . '/../forms/moment_publication_form.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'agreement_manager/component/viewer.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'agreement_manager/component/moment_viewer.class.php';


class InternshipOrganizerMomentPublisher
{
    
    const SINGLE_MOMENT_TYPE = 'single';
    const MULTIPLE_MOMENT_TYPE = 'multiple';
    
    private $parent;
    private $type;

    function InternshipOrganizerMomentPublisher($parent, $type)
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
                $html[] = '<li><img src="' . Theme :: get_common_image_path() . 'treemenu_types/' . $content_object->get_type() . '.png" alt="' . htmlentities(Translation :: get(ContentObject :: type_to_class($content_object->get_type()) . 'TypeName')) . '"/> ' . $content_object->get_title() . '</li>';
            }
            
            $html[] = '</ul>';
            $html[] = '</div>';
            $html[] = '</div>';
        }
          
        $parameters = $this->parent->get_parameters();
        $parameters[RepoViewer :: PARAM_ID] = $ids;
        $parameters[RepoViewer :: PARAM_ACTION] = RepoViewer :: ACTION_PUBLISHER;
       
        $form = new InternshipOrganizerMomentPublicationForm(InternshipOrganizerMomentPublicationForm :: TYPE_MULTI, $ids, $this->parent->get_user(), $this->parent->get_url($parameters), $this->type);
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
            
            if ($this->type == self :: SINGLE_MOMENT_TYPE)
            {
                $moment_id = $this->parent->get_parameter(InternshipOrganizerAgreementManager :: PARAM_MOMENT_ID);
            	$this->parent->redirect($message, (! $publication ? true : false), array(InternshipOrganizerAgreementManager :: PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_VIEW_MOMENT, InternshipOrganizerAgreementManager :: PARAM_MOMENT_ID => $moment_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerAgreementManagerMomentViewerComponent :: TAB_PUBLICATIONS));
            }
            
            if ($this->type == self :: MULTIPLE_MOMENT_TYPE)
            {
                $agreement_id = $this->parent->get_parameter(InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID);
            	$this->parent->redirect($message, (! $publication ? true : false), array(InternshipOrganizerAgreementManager :: PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_VIEW_AGREEMENT, InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID => $agreement_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerAgreementManagerViewerComponent :: TAB_MOMENTS));
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