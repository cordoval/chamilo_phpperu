<?php
/**
 * $Id: logger.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager
 * @author Van Wayenbergh David
 * @author Sven Vanpoucke
 */
require_once (Path :: get_library_path() . 'filesystem/path.class.php');

class Logger
{
    private $filename;
    private $file;
    private $begin;

    /**
     * Constructor for creating a logfile
     */
    function Logger($filename, $append = false)
    {
        $this->filename = Path :: get('SYS_PATH') . '/migration/logfiles/' . $filename;
        Filesystem :: create_dir(dirname($filename));
        $this->file = fopen($this->filename, $append ? 'a+' : 'w+');
    }

    /**
     * add a message to a logfile
     * @param String $message add a message to a logfile
     */
    function add_message($message)
    {
        fwrite($this->file, $this->get_timestamp() . $message . "\n");
    }

    /**
     * Write text without the use of a timestamp
     * @param string $text the text you want to write
     */
    function write_text($text)
    {
        fwrite($this->file, $text . "\n");
    }

    /**
     * Method to check wether a certain piece of text is in the file
     * @param string $text text to check for
     * @return bool true if text is in file
     */
    function is_text_in_file($text)
    {
        if (! $this->file)
        {
            return;
        }
        
        while (! feof($this->file))
        {
            $line = trim(fgets($this->file));
            $line = trim($line);
            if (strcmp($line, $text) == 0)
                return true;
        }
        
        return false;
    }

    /**
     * close the log file
     */
    function close_file()
    {
        fclose($this->file);
        chmod($this->file, 0777);
    }

    /**
     * returns the path of a logfile
     * @return $filename the directory and filename of the logfile
     */
    function get_log_path()
    {
        return $this->filename;
    }

    /**
     * function to get the microtime
     */
    function get_microtime()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }

    /**
     * function to set the start time
     */
    function set_start_time()
    {
        $this->begin = $this->get_microtime();
    }

    /**
     * function to write the used time
     */
    function write_passed_time()
    {
        $passedtime = 0;
        $passedtime = (int) ($this->get_microtime() - $this->begin);
        
        $this->add_message('Passed Time: ' . $passedtime . ' s');
        
        $timefile = fopen($this->filename = Path :: get('SYS_PATH') . '/migration/logfiles/time.txt', 'r');
        
        if (! $timefile)
        {
            return $this->convert_passed_time($passedtime);
        }
        
        $totaltime = 0;
        
        if (! feof($timefile))
            $totaltime = (int) trim(fgets($timefile));
        
        fclose($timefile);
        
        $timefile = fopen($this->filename = Path :: get('SYS_PATH') . '/migration/logfiles/time.txt', 'w');
        if (! $timefile)
        {
            return $this->convert_passed_time($passedtime);
        }
        
        $totaltime += $passedtime;
        
        fwrite($timefile, $totaltime);
        
        fclose($timefile);
        
        return $this->convert_passed_time($passedtime);
    }

    /**
     * Returns the total time passed from the time.txt file
     */
    static function get_total_time_passed()
    {
        $timefile = fopen(Path :: get('SYS_PATH') . '/migration/logfiles/time.txt', 'r');
        if (! $timefile)
        {
            return;
        }
        
        $totaltime = 0;
        
        if (! feof($timefile))
            $totaltime = (int) trim(fgets($timefile));
        
        return $totaltime;
    }

    /**
     * function to get the timestamps
     * @return String returns the timestamps used in a log file
     */
    function get_timestamp()
    {
        setlocale(LC_TIME, 0);
        $timestamp = strftime("[%H:%M:%S] ", time());
        return $timestamp;
    }

    /**
     * Convert the passed time in seconds to h:m:s or m:s or s
     * @param String $passedTime
     * @return converted passed time
     */
    function convert_passed_time($passed_time)
    {
        if ($passed_time / 3600 < 1 && $passed_time / 60 < 1)
        {
            $converted_time = $passed_time . 's';
        }
        else
        {
            if ($passed_time / 3600 < 1)
            {
                $min = (int) ($passed_time / 60);
                $sec = $passed_time % 60;
                $converted_time = $min . 'm ' . $sec . 's';
            }
            else
            {
                $hour = (int) ($passed_time / 3600);
                $rest = $passed_time % 3600;
                $min = (int) ($rest / 60);
                $sec = $rest % 60;
                $converted_time = $hour . 'h ' . $min . 'm ' . $sec . 's';
            }
        }
        return $converted_time;
    }
}
?>