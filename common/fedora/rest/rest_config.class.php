<?php 

/**
 * Contains configuration parameters used by the rest_client class.
 * 
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch, Nicolas Rod
 *
 */
class RestConfig{
	
    const MODE_CURL               = 'MODE_CURL';
    const MODE_PEAR               = 'MODE_PEAR';
    
    public static function get_test_config(){
    	$result =  new self('http://localhost:8080/fedora', '', '');
    	$result->set_return_pid_namespace('changeme');
    	$result->set_object_datastream_name('file');//@todo chanage that
    	return $result;
    }
    
    /**
    * The connexion mode to the REST service
    *
    * @var string
    */ 
    private $connexion_mode;
    
    /**
    * The URL of the REST service
    *
    * @var string
    */ 
    private $base_url = '';
    
    /**
    * the basic authentication login
    *
    * @var string
    */ 
    private $basic_login = '';
    
    /**
    * the basic authentication password
    *
    * @var string
    */ 
    private $basic_password = '';
        
    /**
    * client certificate path to use. The file may contain the certificate and the key as well.
    * The certificate format must be PEM.
    *
    * @var string
    */ 
    private $client_certificate_file = '';
    
    /**
    * client certificate key path
    *
    * @var string
    */ 
    private $client_certificate_key_file = '';
    
    /**
    * client certificate key password used for authentication
    *
    * @var string
    */ 
    private $client_certificate_key_password = '';
    
    /**
    * the checking of the target certificate
    *
    * @var bool
    */ 
    private $check_target_certificate = false;
    
    /**
    * the path to additional CA certificates used to verify the target certificate
    *
    * @var string
    */ 
    private $target_ca_file = '';
    
    /**
     * @var bool
     */
    private $return_system_objects = false;
    
    /**
     * 
     * @var int
     */
    private $max_results = 250;
    
    /**
     * 
     * @var string
     */
    private $return_pid_namespace = '';
    
    /**
     * 
     * @var bool
     */
    private $return_state = 'A';
    
    private $object_datastream_name = 'file'; 
    
    public function __construct($base_url ='', $login = '', $password = ''){
        $this->set_default_mode();
        $this->base_url = $base_url;
        $this->basic_login = $login;
        $this->basic_password = $password;
    }
    
    /**
     * Check if the cURL extension is installed. If not, the PEAR mode is used
     * 
     * @return void
     */
    protected function set_default_mode(){
        if(extension_loaded('curl')){
            $this->connexion_mode = self::MODE_CURL;
        }else{
            $this->connexion_mode = self::MODE_PEAR;
        }
    }
    
    /**
    * Get the connexion mode to the REST service
    *
    * @return string
    */
    public function get_connexion_mode()
    {
    	return $this->connexion_mode;
    }
    
    /**
    * Set the connexion mode to the REST service
    *
    * @var $connexion_mode string
    * @return string
    */
    public function set_connexion_mode($connexion_mode){
        if($connexion_mode == self::MODE_CURL || $connexion_mode == self::MODE_PEAR){
            $this->connexion_mode = $connexion_mode;
        }else{
            $this->set_default_mode();
        }
        return $this->connexion_mode;
    }

    /**
     * True if queries should return system objects. I.e. fedora-system
     */
    public function get_return_system_objects(){
    	return $this->return_system_objects;
    }
    
    public function set_return_sytem_objects($value){
    	$this->return_system_objects = $value;
    }
    
    /**
     * Maximum number of objects returned by queries
     */
    public function get_max_results(){
    	return $this->max_results;
    }
    
    public function set_max_results($value){
    	$this->max_results = max($value, 0);
    }
    
    /**
     * If set returns only objects with pid having this namespace
     */
    public function get_return_pid_namespace(){
    	return $this->return_pid_namespace;
    }
    
    public function set_return_pid_namespace($value){
    	$this->return_pid_namespace = $value;
    }
    
    /**
     * If set returns only objects that match the selected state:
     * A - active
     * I - inactive
     * D - deleted
     * 
     */
    public function get_return_state(){
    	return $this->return_state;
    }
    
    public function set_return_state($value){
    	$this->return_state = $value;
    }
    
    public function get_object_datastream_name(){
    	return $this->object_datastream_name;
    }
    
    public function set_object_datastream_name($value){
    	$this->object_datastream_name = $value;
    }
    
    /**
    * Get The URL of the REST service
    *
    * @return string
    */
    public function get_base_url(){
    	return $this->base_url;
    }
    
    /**
    * Set The URL of the REST service
    *
    * @var $url string
    * @return void
    */
    public function set_base_url($url){
    	$this->base_url = $url;
    }
       
    /**
    * Get the basic authentication login
    *
    * @return string
    */
    public function get_basic_login(){
    	return $this->basic_login;
    }
    
    /**
    * Set the basic authentication login
    *
    * @var $basic_login string
    * @return void
    */
    public function set_basic_login($basic_login){
    	$this->basic_login = $basic_login;
    }
        
    /**
    * Get the basic authentication password
    *
    * @return string
    */
    public function get_basic_password(){
    	return $this->basic_password;
    }
    
    /**
    * Set the basic authentication password
    *
    * @var $basic_password string
    * @return void
    */
    public function set_basic_password($basic_password){
    	$this->basic_password = $basic_password;
    }
        
    /**
    * Get client certificate path to use. The file may contain the certificate only or the certicate with the key.
    * The certificate must be in PEM format.
    * 
    * @return string
    */
    public function get_client_certificate_file(){
    	return $this->client_certificate_file;
    }
    
    /**
    * Set client certificate file to use to authenticate the REST request
    *
    * @var $client_certificate_file string
    * @return void
    */
    public function set_client_certificate_file($client_certificate_file){
    	$this->client_certificate_file = $client_certificate_file;
    }
        
    /**
    * Get client certificate key file
    *
    * @return string
    */
    public function get_client_certificate_key_file(){
    	return $this->client_certificate_key_file;
    }
    
    /**
    * Set client certificate key file to use to authenticate the REST request
    *
    * @var $client_certificate_key_file string
    * @return void
    */
    public function set_client_certificate_key_file($client_certificate_key_file){
    	$this->client_certificate_key_file = $client_certificate_key_file;
    }
        
    /**
    * Get client certificate key password used for authentication
    *
    * @return string
    */
    public function get_client_certificate_key_password(){
    	return $this->client_certificate_key_password;
    }
    
    /**
    * Set client certificate key password used for authentication
    *
    * @var $client_certificate_key_password string
    * @return void
    */
    public function set_client_certificate_key_password($client_certificate_key_password){
    	$this->client_certificate_key_password = $client_certificate_key_password;
    }
        
	/**
    * Get the checking of the target certificate
    *
    * @return bool
    */
    public function get_check_target_certificate(){
    	return $this->check_target_certificate;
    }
    
    /**
    * Set the checking of the target certificate
    *
    * @var $check_target_certificate bool
    * @return void
    */
    public function set_check_target_certificate($check_target_certificate){
    	$this->check_target_certificate = $check_target_certificate;
    }

    /**
    * Get the file containing CA certificates used to verify the target certificate identity
    *
    * @return string
    */
    public function get_target_ca_file()
    {
    	return $this->target_ca_file;
    }
    
    /**
    * Set the file containing CA certificates used to verify the target certificate identity
    *
    * @var $target_ca_file string
    * @return void
    */
    public function set_target_ca_file($target_ca_file)
    {
    	$this->target_ca_file = $target_ca_file;
    }
    
}

?>