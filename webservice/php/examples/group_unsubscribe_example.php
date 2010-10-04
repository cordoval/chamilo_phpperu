<?php
require_once dirname(__FILE__) . '/../../plugin/nusoap/nusoap.php';
ini_set('max_execution_time', - 1);
ini_set('memory_limit', - 1);
$time_start = microtime(true);
/*
 * Change to the location of the .csv file which you wish to use.
 */
$file = dirname(__FILE__) . '/group_unsubscribe.csv';
$group = parse_csv($file);
/*
 * change location to the location of the test server
 */
$location = 'http://demo2.chamilo.org/group/webservices/webservices_group.class.php?wsdl';
$client = new nusoap_client($location, 'wsdl');
$hash = '';

unsubscribe_user($group[0]);

$time_end = microtime(true);
$time = $time_end - $time_start;

echo "Execution time was  $time seconds\n";

function parse_csv($file)
{
    if (file_exists($file) && $fp = fopen($file, "r"))
    {
        $keys = fgetcsv($fp, 1000, ";");
        $groups = array();
        while ($group_data = fgetcsv($fp, 1000, ";"))
        {
            $group = array();
            foreach ($keys as $index => $key)
            {
                $group[$key] = trim($group_data[$index]);
            }
            $groups[] = $group;
        }
        fclose($fp);
    }
    else
    {
        log("ERROR: Can't open file ($file)");
    }
    return $groups;
}

function unsubscribe_user($group_rel_user)
{
    global $hash, $client;
    log_message('Unsubscribing user ' . $group_rel_user['user_id'] . ' to group ' . $group_rel_user['group_id']);
    
    /*
     * If the hash is empty, request a new one. Else use the existing one.
     * Expires by default after 10 min.
     */
    if (empty($hash))
        $hash = login();
    $result = $client->call('WebServicesGroup.unsubscribe_user', array('input' => $group_rel_user, 'hash' => $hash));
    if ($result == 1)
    {
        log_message(print_r('User successfully unsubscribed', true));
    }
    else
        log_message(print_r($result, true));
}

function login()
{
    global $client;
    
    /* Change the username and password to the ones corresponding to  your database.
     * The password for the login service is :
     * IP = the IP as seen by the target Chamilo server from where the call to the webservice is made.
     * You can request this through e.g. http://www.whatsmyip.org/
     * PW = your hashed password from the db
     * The hashing algoritm needs to be the same as the one used on the Chamilo installation you're trying to communicate with.
     * When in doubt, ask the administrator of said installation.
     * $password = Hash(IP+PW) ;
     */
    
    $username = 'Samumon';
    $password = hash('sha1', '193.190.172.141' . hash('sha1', '60d9efdb7c'));
    
    /*
     * change location to server location for the wsdl
     */
    
    $login_client = new nusoap_client('http://demo2.chamilo.org/user/webservices/login_webservice.class.php?wsdl', 'wsdl');
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
    echo date('[H:m] ', time()) . $text . '<br />';
}

function debug($client)
{
    echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
    echo '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
    echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
}

?>