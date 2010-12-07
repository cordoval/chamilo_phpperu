<?php
namespace application\context_linker;
use common\libraries\Request;
use common\libraries\Translation;
use application\metadata\MetadataDataManager;
use common\libraries\EqualityCondition;
use application\metadata\MetadataPropertyValue;
use application\metadata\ContentObjectMetadataPropertyValue;
use common\libraries\Utilities;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
/**
 * Component to create a new context_link object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class ContextLinkerManagerContextLinkPublisherComponent extends ContextLinkerManager
{
    /**
     * Runs this component and displays its output.
     */

    private $content_object;
    

    function run()
    {
        $trail = new BreadcrumbTrail;
        $trail->add(new Breadcrumb($this->get_url(array(ContextLinkerManager :: PARAM_ACTION => null)), Translation :: get('ContextLinker')));
        $trail->add(new Breadcrumb(Translation :: get('CreateObject', array('OBJECT' => Translation::get('ContextLink')), Utilities::COMMON_LIBRARIES)));
        $trail->add_help('ContextLinkCreator');

        $original_id = Request :: get(ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID);
        $alternative_id = Request :: get(ContextLinkerManager :: PARAM_ALTERNATIVE_CONTENT_OBJECT_ID);
        //check that same object is'nt selected
        if($original_id == $alternative_id)
        {
            $params =array();
            $params[ContextLinkerManager :: PARAM_ACTION] = ContextLinkerManager :: ACTION_CREATE_CONTEXT_LINK;
            $params[ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID] = $original_id;

            $this->redirect(Translation :: get('SameContentObjectSelected'), 1, $params);
            
            exit();
        }

        $context_link = new ContextLink();
        $context_link->set_original_content_object_id($original_id);
        $context_link->set_alternative_content_object_id($alternative_id);
        $context_link->set_date(time());

        $mdm = MetadataDataManager :: get_instance();

        $condition = new EqualityCondition(ContentObjectMetadataPropertyValue :: PROPERTY_CONTENT_OBJECT_ID, Request :: get(ContextLinkerManager :: PARAM_ALTERNATIVE_CONTENT_OBJECT_ID));
        $metadata_property_values = $mdm->retrieve_full_content_object_metadata_property_values($condition);
        
        $params = array();
        $params[ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID] = Request :: get(ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID);
        $params[ContextLinkerManager :: PARAM_ALTERNATIVE_CONTENT_OBJECT_ID] = Request ::get(ContextLinkerManager :: PARAM_ALTERNATIVE_CONTENT_OBJECT_ID);

        $original_form = new ContextLinkForm('context_link_form_original', ContextLinkForm :: TYPE_ORIGINAL, $context_link, null, $this->get_url($params));
        $alternative_form = new ContextLinkForm('context_link_form_alternative', ContextLinkForm :: TYPE_ALTERNATIVE, &$context_link, $metadata_property_values, $this->get_url($params));

        if($alternative_form->validate())
        {
            $success = $alternative_form->create_context_link();
            $this->redirect($success ? Translation :: get('ObjectCreated', array('OBJECT' => Translation :: get('ContextLink')), Utilities :: COMMON_LIBRARIES) : Translation :: get('ObjectNotCreated', array('OBJECT' => Translation :: get('ContextLink')), Utilities :: COMMON_LIBRARIES), !$success, array(ContextLinkerManager :: PARAM_ACTION => ContextLinkerManager :: ACTION_BROWSE_CONTEXT_LINKS, ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID => Request :: get(ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID)));
        }
        elseif($original_form->validate())
        {
            if($success = $original_form->create_metadata_property_value())
            {
                $this->redirect(Translation :: get('ObjectCreated', array('OBJECT' => Translation :: get('MetadataPropertyValue')), Utilities :: COMMON_LIBRARIES), false, $params);
            }
            else
            {
                $this->redirect(Translation :: get('ObjectNotCreated', array('OBJECT' => Translation :: get('MetadataPropertyValue')), Utilities :: COMMON_LIBRARIES) . implode("\n", $context_link->get_errors()), true, $params);
            }
        }
        else
        {
            $this->display_header($trail);
            $html = array();
            $html[] = '<table><tr><td><h4>'.Translation :: get('OriginalContentObject').'</h4>';
            $html[] = '<div>' . $this->get_content_object_metadata_output(Request :: get(ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID)) . '</div>';
            $html[] = $original_form->toHtml() . '</td>';
            $html[] = '<td><h4>'.Translation :: get('AlternativeContentObject').'</h4>' . $alternative_form->toHtml() . '</td></tr></table>';

            echo implode("\n", $html);

            $this->display_footer();
        }
    }

    function get_content_object_metadata_output($content_object_id)
    {
        $mdm = MetadataDataManager :: get_instance();

        $condition = new EqualityCondition(MetadataPropertyValue :: PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
        $metadata_property_values = $mdm->retrieve_full_content_object_metadata_property_values($condition);

        foreach($metadata_property_values as $id => $value)
        {
            $metadata .= $value . '<br />';
        }
        return $metadata;
    }

}
?>