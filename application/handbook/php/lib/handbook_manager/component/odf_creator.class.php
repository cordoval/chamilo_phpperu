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
                                    'content' => 'test content 1',
                                    'image' => dirname(__FILE__) . '/handbook_odf_creator/test_images/1.jpg');
            $topics_list[] = array('title' => 'test title 2',
                                    'content' => 'test content 2',
                                    'image' => dirname(__FILE__) . '/handbook_odf_creator/test_images/2.jpg');
            $topics_list[] = array('title' => 'test title 3',
                                    'content' => 'test content 3',
                                    'image' => dirname(__FILE__) . '/handbook_odf_creator/test_images/3.jpg');
            $topics_list[] = array('title' => 'test title 4',
                                    'content' => 'test content 4',
                                    'image' => dirname(__FILE__) . '/handbook_odf_creator/test_images/4.jpg');
            $topics_list[] = array('title' => 'test title 4',
                                    'content' => 'test content 4',
                                    'image' => dirname(__FILE__) . '/handbook_odf_creator/test_images/4.jpg');


            $levels2_list = array();


            $levels2_list[1][] = array('title' => 'test title 1.1',
                                        'content' => 'test content 1.1',
                                        'image' => dirname(__FILE__) . '/handbook_odf_creator/test_images/test.jpg');

            $levels2_list[1][] = array('title' => 'test title 1.2',
                                        'content' => 'test content 1.2',
                                        'image' => dirname(__FILE__) . '/handbook_odf_creator/test_images/test.jpg');
            $levels2_list[1][] = array('title' => 'test title 1.3',
                                        'content' => 'test content 1.3',
                                        'image' => dirname(__FILE__) . '/handbook_odf_creator/test_images/test.jpg');
            $levels2_list[2][] = array('title' => 'test title 2.1',
                                        'content' => 'test content 2.1',
                                        'image' => dirname(__FILE__) . '/handbook_odf_creator/test_images/test.jpg');
            $levels2_list[2][] = array('title' => 'test title 2.2',
                                        'content' => 'test content 2.2',
                                        'image' => dirname(__FILE__) . '/handbook_odf_creator/test_images/test.jpg');
            $levels2_list[3][] = array('title' => 'test title 3.1',
                                        'content' => 'test content 3.1',
                                        'image' => dirname(__FILE__) . '/handbook_odf_creator/test_images/test.jpg');
            $levels2_list[4][] = array('title' => 'test title 4.1',
                                        'content' => 'test content 4.1',
                                        'image' => dirname(__FILE__) . '/handbook_odf_creator/test_images/test.jpg');
            $levels2_list[4][] = array('title' => 'test title 4.2',
                                        'content' => 'test content 4.2',
                                        'image' => dirname(__FILE__) . '/handbook_odf_creator/test_images/test.jpg');


		$odf = new Odf($template_file);
                $odf->setVars('title', 'titel van mijn bestand');
                $message = 'test message for odt document creation test test test';
                $odf->setVars('message', $message);

                $topics = $odf->setSegment('level1');
                $i=1;
                foreach ($topics_list as $element)
                {
                    $topics->title($element['title']);
                    $topics->content($element['content']);
//                    $topics->setImage('image', dirname(__FILE__) . '/handbook_odf_creator/test_images/test.jpg');
//                    $topics->setImageReplace('test.jpg', $element['image']);
                    $topics->setImage('image', $element['image']);

                    $o = '2';
                    foreach($levels2_list[$i] as $level2_element)
                    {
                        $test = 'level'.$o;
                        $topics->$test->setVars('title'.$o, $level2_element['title']);
                        $topics->$test->setVars('content'.$o, $level2_element['content']);
//                        $topics->level2->title.$o($level2_element['title']);
//                        $topics->level2->content.$o($level2_element['content']);
                        $topics->$test->setImage('image'.$o, $level2_element['image']);
                        $topics->$test->merge();
                    }


                    $topics->merge();
                    $i++;
                }

                $odf->mergeSegment($topics);
                
                $odf->exportAsAttachedFile();
	}

}
?>