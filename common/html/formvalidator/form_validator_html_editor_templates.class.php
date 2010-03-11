<?php
/**
 * The combination of options available for the FormValidatorHtmlEditor
 * Should be implemented for each specific editor to translate the generic option values
 *
 * @author Scaramanga
 */

abstract class FormValidatorHtmlEditorTemplates
{

    /**
     * @param Array $options
     */
    function __construct()
    {
    }

    function get_templates()
    {
	    $rdm = RepositoryDataManager :: get_instance();

    	$conditions = array();
    	$conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, Session :: get_user_id());
    	$conditions[] = new EqualityCondition(ContentObject :: PROPERTY_TYPE, 'template');
    	$condition = new AndCondition($conditions);

    	return $rdm->retrieve_content_objects($condition, array(new ObjectTableOrder(ContentObject :: PROPERTY_TITLE)));
    }

    function template_object_exists()
    {
        $adm = AdminDataManager :: get_instance();

        $conditions = array();
        $conditions[] = new EqualityCondition(Registration :: PROPERTY_TYPE, Registration :: TYPE_CONTENT_OBJECT);
        $conditions[] = new EqualityCondition(Registration :: PROPERTY_NAME, 'template');
        $condition = new AndCondition($conditions);

        return $adm->count_registrations($condition);
    }

    /**
     * @param String $type
     * @param Array $options
     * @return FormValidatorHtmlEditorOptions
     */
    public static function factory($type)
    {
        $file = dirname(__FILE__) . '/html_editor_templates/' . $type . '_html_editor_templates.class.php';
        $class = 'FormValidator' . Utilities :: underscores_to_camelcase($type) . 'HtmlEditorTemplates';

        if (file_exists($file))
        {
            require_once ($file);
            return new $class();
        }
        else
        {
            return null;
        }
    }
}
?>