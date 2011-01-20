<?php

namespace application\handbook;

use Odf;



require_once dirname(__FILE__).'/../../../../../../common/libraries/plugin/odtPHP/library/odf.php';
require_once dirname(__FILE__).'/../handbook_manager.class.php';

/**
 * Handbook component to create odf documents from handbook-information
 * @author Nathalie Blocry
 */
class HandbookManagerOdfCreatorComponent extends HandbookManager
{

	function run()
	{
            $template_file = dirname(__FILE__) . '/handbook_odf_creator/handbook_template.odt';

            $topics_list = array();
            $topics_list[] = array('title' => 'test title 1',
                                    'content' => 'test content 1');
            $topics_list[] = array('title' => 'test title 2',
                                    'content' => 'test content 2');
            $topics_list[] = array('title' => 'test title 3',
                                    'content' => 'test content 3');
            $topics_list[] = array('title' => 'test title 4',
                                    'content' => 'test content 4');
            $topics_list[] = array('title' => 'test title 4',
                                    'content' => 'test content 4');




		$odf = new Odf($template_file);
                $odf->setVars('title', 'titel van mijn bestand');
                $message = 'test message for odt document creation test test test';
                $odf->setVars('message', $message);

                $topics = $odf->setSegment('topics');
                foreach ($topics_list as $element)
                {
                    $topics->titleTopic($element['title']);
                    $topics->contentTopic($element['content']);
                    $topics->merge();
                }

                $odf->mergeSegment($topics);
                
                $odf->exportAsAttachedFile();
	}

}
?>