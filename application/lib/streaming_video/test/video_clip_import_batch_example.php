<?php
include_once (dirname(__FILE__) . '/../../../../common/global.inc.php');
require_once Path :: get_repository_path().'lib/content_object/streaming_video_clip/streaming_video_clip.class.php';
$file = dirname(__FILE__) . '/video_clip_import.csv';
$users = parse_csv($file);


create_videos($users);

$time_end = microtime(true);
$time = $time_end - $time_start;

echo "Execution time was  $time seconds\n";

function parse_csv($file)
{
    if (file_exists($file) && $fp = fopen($file, "r"))
    {
        $keys = fgetcsv($fp, 1000, ";");
        $users = array();
        
        while ($video_data = fgetcsv($fp, 1000, ";"))
        {
            $video = array();
            foreach ($keys as $index => $key)
            {
                $video[$key] = trim($video_data[$index]);
            }
            $videos[] = $video;
        }
        fclose($fp);
    }
    else
    {
        log("ERROR: Can't open file ($file)");
    }
    
    return $videos;
}

function create_videos(&$videos)
{

    foreach($videos as $video)
    {
        $clip = new StreamingVideoClip();
        $clip->set_title($video['title']);
        $clip->set_owner_id(Session :: get_user_id());
        $clip->set_aspect_ratio($video['aspect_ratio']);
        $clip->set_duration($video['duration']);
        $clip->set_conversion_state($video['conversion_state']);
        if ($clip->create())
        {
            log_message(print_r('Video successfully created', true));
        }
        else
        {
            log_message(print_r($result, true));
        }
    }

    

}



/*function dump($value)
{
    echo '<pre>';
    print_r($value);
    echo '</pre>';
}*/

function log_message($text)
{
    echo date('[H:m:s] ', time()) . $text . '<br />';
}

/*function debug($client)
{
    echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
    echo '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
    echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
}*/

?>