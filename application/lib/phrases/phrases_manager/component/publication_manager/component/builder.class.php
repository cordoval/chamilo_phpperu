<?php
/**
 * $Id: updater.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.assessment_manager.component
 */
require_once dirname(__FILE__) . '/../../../../forms/phrases_publication_form.class.php';

/**
 * Component to edit an existing assessment_publication object
 * @author Hans De Bisschop
 * @author
 */
class PhrasesPublicationManagerBuilderComponent extends PhrasesPublicationManager
{
    private $content_object;

    function run()
    {
        $publication_id = Request :: get(PhrasesPublicationManager :: PARAM_PHRASES_PUBLICATION_ID);
        $publication = $this->retrieve_phrases_publication($publication_id);
        $this->content_object = $publication->get_publication_object();
        $this->set_parameter(PhrasesPublicationManager :: PARAM_PHRASES_PUBLICATION_ID, $publication_id);

        $new_trail = BreadcrumbTrail :: get_instance();
//        $new_trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS)), Translation :: get('BrowseAssessmentPublications')));

        $complex_builder = ComplexBuilder :: factory($this, $this->content_object->get_type());
        $complex_builder->run();
    }

    function get_root_content_object()
    {
        return $this->content_object;
    }
}
?>