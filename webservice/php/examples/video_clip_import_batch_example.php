<?php
require_once dirname(__FILE__) . '/../../plugin/nusoap/nusoap.php';
ini_set('max_execution_time', - 1);
ini_set('memory_limit', - 1);
$time_start = microtime(true);

$file = dirname(__FILE__) . '/video_clip_import.csv';
$users = parse_csv($file);
/*
 * change location to the location of the test server
 */
//$location = 'http://localhost/user/webservices/webservices_user.class.php?wsdl';
$location = 'http://localhost/chamilo_2.0/application/lib/streaming_video/webservices/webservices_video_clip.class.php?wsdl';
$client = new nusoap_client($location, 'wsdl');
$hash = '';

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
    global $hash, $client;
    log_message('Creating videos ');
    if ($hash == '')
        $hash = login();
    
    $result = $client->call('WebservicesVideoClip.create_clips', array('input' => $videos, 'hash' => $hash));
    if ($result == 1)
    {
        log_message(print_r('Videos successfully created', true));
    }
    else
        log_message(print_r($result, true));

}

function login()
{
    global $client;
    
    /* Change the username and password to the ones corresponding to  your database.
     * The password for the login service is :
     * IP = the ip from where the call to the webservice is made
     * PW = your hashed password from the db
     *
     * $password = Hash(IP+PW) ;
     */
    
    $username = 'admin';
    //$username = 'Soliber';
    
    $password = '010b832bca63fd107d75ed119788c508f63dee6f';
    //$password = hash('sha1', '192.168.1.101' . hash('sha1', '60d9efdb7c'));
    //$password = hash('sha1','127.0.0.1'.hash('sha1','werk'));
    

    /*
     * change location to server location for the wsdl
     */
    
    //$login_client = new nusoap_client('http://demo2.chamilo.org/user/webservices/login_webservice.class.php?wsdl', 'wsdl');
    $login_client = new nusoap_client('http://localhost/user/webservices/login_webservice.class.php?wsdl', 'wsdl');
    $result = $login_client->call('LoginWebservice.login', array('input' => array('username' => $username, 'password' => $password), 'hash' => ''));
    log_message(print_r($result, true));
    if (is_array($result) && array_key_exists('hash', $result))
        return $result['hash']; //hash 3
    

    return '';

}

function dump($value)
{
    echo '<pre>';
    print_r($value);
    echo '</pre>';
}

function log_message($text)
{
    echo date('[H:m:s] ', time()) . $text . '<br />';
}

function debug($client)
{
    echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
    echo '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
    echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
}

?>