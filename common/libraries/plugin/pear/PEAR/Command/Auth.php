<?php
/**
 * PEAR_Command_Auth (login, logout commands)
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   pear
 * @package    PEAR
 * @author     Stig Bakken <ssb@php.net>
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  1997-2008 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    CVS: $Id: Auth.php 137 2009-11-09 13:24:37Z vanpouckesven $
 * @link       http://pear.php.net/package/PEAR
 * @since      File available since Release 0.1
 */

/**
 * base class
 */
require_once 'PEAR/Command/Common.php';
require_once 'PEAR/Config.php';

/**
 * PEAR commands for login/logout
 *
 * @category   pear
 * @package    PEAR
 * @author     Stig Bakken <ssb@php.net>
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  1997-2008 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: 1.7.2
 * @link       http://pear.php.net/package/PEAR
 * @since      Class available since Release 0.1
 */
class PEAR_Command_Auth extends PEAR_Command_Common
{
    // {{{ properties

    var $commands = array(
        'login' => array(
            'summary' => 'Connects and authenticates to remote server',
            'shortcut' => 'li',
            'function' => 'doLogin',
            'options' => array(),
            'doc' => '<channel name>
Log in to a remote channel server.  If <channel name> is not supplied, 
the default channel is used. To use remote functions in the installer
that require any kind of privileges, you need to log in first.  The
username and password you enter here will be stored in your per-user
PEAR configuration (~/.pearrc on Unix-like systems).  After logging
in, your username and password will be sent along in subsequent
operations on the remote server.',
            ),
        'logout' => array(
            'summary' => 'Logs out from the remote server',
            'shortcut' => 'lo',
            'function' => 'doLogout',
            'options' => array(),
            'doc' => '
Logs out from the remote server.  This command does not actually
connect to the remote server, it only deletes the stored username and
password from your user configuration.',
            )

        );

    // }}}

    // {{{ constructor

    /**
     * PEAR_Command_Auth constructor.
     *
     * @access public
     */
    function __construct(&$ui, &$config)
    {
        parent :: __construct($ui, $config);
    }

    // }}}

    // {{{ doLogin()

    /**
     * Execute the 'login' command.
     *
     * @param string $command command name
     *
     * @param array $options option_name => value
     *
     * @param array $params list of additional parameters
     *
     * @return bool TRUE on success or
     * a PEAR error on failure
     *
     * @access public
     */
    function doLogin($command, $options, $params)
    {
        $reg = &$this->config->getRegistry();
        
        // If a parameter is supplied, use that as the channel to log in to
        if (isset($params[0])) {
            $channel = $params[0];
        } else {
            $channel = $this->config->get('default_channel');
        }
        
        $chan = $reg->getChannel($channel);
        if (PEAR::isError($chan)) {
            return $this->raiseError($chan);
        }
        $server = $this->config->get('preferred_mirror', null, $channel);
        $remote = &$this->config->getRemote();
        $username = $this->config->get('username', null, $channel);
        if (empty($username)) {
            $username = isset($_ENV['USER']) ? $_ENV['USER'] : null;
        }
        $this->ui->outputData("Logging in to $server.", $command);
        
        list($username, $password) = $this->ui->userDialog(
            $command,
            array('Username', 'Password'),
            array('text',     'password'),
            array($username,  '')
            );
        $username = trim($username);
        $password = trim($password);

        $ourfile = $this->config->getConfFile('user');
        if (!$ourfile) {
            $ourfile = $this->config->getConfFile('system');
        }

        $this->config->set('username', $username, 'user', $channel);
        $this->config->set('password', $password, 'user', $channel);

        if ($chan->supportsREST()) {
            $ok = true;
        } else {
            $remote->expectError(401);
            $ok = $remote->call('logintest');
            $remote->popExpect();
        }
        if ($ok === true) {
            $this->ui->outputData("Logged in.", $command);
            // avoid changing any temporary settings changed with -d
            $ourconfig = new PEAR_Config($ourfile, $ourfile);
            $ourconfig->set('username', $username, 'user', $channel);
            $ourconfig->set('password', $password, 'user', $channel);
            $ourconfig->store();
        } else {
            return $this->raiseError("Login failed!");
        }
        return true;
    }

    // }}}
    // {{{ doLogout()

    /**
     * Execute the 'logout' command.
     *
     * @param string $command command name
     *
     * @param array $options option_name => value
     *
     * @param array $params list of additional parameters
     *
     * @return bool TRUE on success or
     * a PEAR error on failure
     *
     * @access public
     */
    function doLogout($command, $options, $params)
    {
        $reg = &$this->config->getRegistry();
        $channel = $this->config->get('default_channel');
        $chan = $reg->getChannel($channel);
        if (PEAR::isError($chan)) {
            return $this->raiseError($chan);
        }
        $server = $this->config->get('preferred_mirror');
        $this->ui->outputData("Logging out from $server.", $command);
        $this->config->remove('username');
        $this->config->remove('password');
        $this->config->store();
        return true;
    }

    // }}}
}

?>