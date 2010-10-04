<?php

require_once dirname(__FILE__) . '/../forms/agreement_publication_form.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/agreement_manager/component/viewer.class.php';

class InternshipOrganizerAgreementPublisher
{
    
    const SINGLE_AGREEMENT_TYPE = 'single';
    const MULTIPLE_AGREEMENT_TYPE = 'multiple';
    
    private $parent;
    private $type;

    function InternshipOrganizerAgreementPublisher($parent, $type)
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
        
        $form = new InternshipOrganizerAgreementPublicationForm(InternshipOrganizerAgreementPublicationForm :: TYPE_MULTI, $ids, $this->parent->get_user(), $this->parent->get_url($parameters), $this->type);
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
            
            if ($this->type == self :: SINGLE_AGREEMENT_TYPE)
            {
                $agreement_id = $this->parent->get_parameter(InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID);
                $this->parent->redirect($message, (! $publication ? true : false), array(InternshipOrganizerAgreementManager :: PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_VIEW_AGREEMENT, InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID => $agreement_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerAgreementManagerViewerComponent :: TAB_PUBLICATIONS));
            }
            
            if ($this->type == self :: MULTIPLE_AGREEMENT_TYPE)
            {
                $this->parent->redirect($message, (! $publication ? true : false), array(InternshipOrganizerAgreementManager :: PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_BROWSE_AGREEMENT));
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