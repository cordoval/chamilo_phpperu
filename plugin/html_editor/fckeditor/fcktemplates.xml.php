<?php
require_once dirname(__FILE__).'/../../../common/global.inc.php';

Translation :: set_application('repository');

$html = array();

if (Authentication :: is_valid())
{
    $adm = AdminDataManager :: get_instance();

    $conditions = array();
    $conditions[] = new EqualityCondition(Registration :: PROPERTY_TYPE, Registration :: TYPE_CONTENT_OBJECT);
    $conditions[] = new EqualityCondition(Registration :: PROPERTY_NAME, 'template');
    $condition = new AndCondition($conditions);

    $template_object_exists = $adm->count_registrations($condition);

    if (!$template_object_exists)
    {
    	$html[] = '<?xml version="1.0" encoding="utf-8" ?>';
    	$html[] = '<Templates>';
    	$html[] = '</Templates>';
    }
    else
    {
	    $rdm = RepositoryDataManager :: get_instance();

    	$conditions = array();
    	$conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, Session :: get_user_id());
    	$conditions[] = new EqualityCondition(ContentObject :: PROPERTY_TYPE, 'template');
    	$condition = new AndCondition($conditions);

    	$templates = $rdm->retrieve_content_objects($condition, array(new ObjectTableOrder(ContentObject :: PROPERTY_TITLE)));

    	$html[] = '<?xml version="1.0" encoding="utf-8" ?>';
    	$html[] = '<Templates>';

    	while ($template = $templates->next_result())
    	{
    		//$html[] = '<Template title="'. $template->get_title() .'" image="'. Theme :: get_common_image_path() .'status_error.png">';
    		$html[] = '<Template title="'. $template->get_title() .'">';
    		$html[] = '<Description>'. $template->get_description() .'</Description>';
    		$html[] = '<Html>';
    		$html[] = '<![CDATA[';
    		$html[] = $template->get_design();
    		$html[] = ']]>';
    		$html[] = '</Html>';
    		$html[] = '</Template>';
    	}

    	$html[] = '</Templates>';
    }
}

echo implode("\n", $html);
?>
