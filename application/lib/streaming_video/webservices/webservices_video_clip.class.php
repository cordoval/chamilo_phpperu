<?php
/*
 * @author tim de pauw
 * @author jevdheyd
 * this class has all webservices for the streaming video application
 *
 * register_upload:
 * - username : id of upload-account
 * - password : password of upload-account
 * - source-file : name of the file on the upload server
 * - segments : transcoding segments
 *
 */

require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once dirname(__FILE__) . '/../../../../common/webservice/webservice.class.php';
require_once dirname(__FILE__) . '/../data_manager/database.class.php';
require_once dirname(__FILE__) . '/../../../../repository/lib/content_object.class.php';
//require_once dirname(__FILE__) . '/../content_object_publication.class.php';
require_once dirname(__FILE__) . '/../../../../repository/lib/content_object/streaming_video_clip/streaming_video_clip.class.php';
require_once dirname(__FILE__) . '/../transcoding.class.php';

ini_set('max_execution_time', - 1);
ini_set('memory_limit', - 1);

$handler = new WebservicesVideoClip();
$handler->run();

class WebservicesVideoClip
{
    private $webservice;
    //private $validator;

    function WebservicesVideoClip()
    {
        $this->webservice = Webservice :: factory($this);
        //$this->validator = Validator :: get_validator('streaming_video_clip');
    }

    function run()
    {
        $functions = array();
       
        $functions['registerUpload'] = array('array_input' => true, 'input' => array(new Transcoding()), 'output'=>new StreamingVideoClip());
        $functions['create_clips'] = array('input' => new StreamingVideoClip());

        $this->webservice->provide_webservice($functions);
    }

    function registerUpload($username, $password, $source_file, $segments)
    {
        if ($this->webservice->can_execute($input_clip, 'register upload'))
        {
            //verify upload account
            $dm = StreamingVideoDataManager :: get_instance();
           
            //create clips
            foreach($segments as $segment)
            {
                $title      = $segment->title;
                $start_time = $segment->startTime;
                $end_time   = $segment->endTime;

                $clip = new StreamingVideoClip();
                $clip->set_title($title);

                if($clip->create())
                {
                    //create new transcoding record
                    $transcoding = new Transcoding();
                    $transcoding->set_clip_id($clip_id);
                    $transcoding->set_start_time($start_time);
                    $transcoding->set_source_file($source_file);
                    return $this->webservice->raise_message($transcoding->create());
                }else{
                    //TODO: see for correct name of NotCreated
                    return $this->webservice->raise_error(Translation :: get('StreamingVideoClip').Translation :: get('NotCreated'));
                }
            }
        }
        else
        {
             return $this->webservice->raise_error($this->webservice->get_message());
        }

    }

    function create_clips(&$input_clip)
    {
            if ($this->webservice->can_execute($input_clip, 'create clips'))
            {
                foreach($input_clip[input] as $clip)
                {
                    $c = new StreamingVideoClip(0,$clip);
                    $c->create();
                }
                return $this->webservice->raise_message(Translation :: get('ClipsCreated') . '.');
            }
            else
            {
                return $this->webservice->raise_error($this->webservice->get_message());
            }
    }
}
?>
