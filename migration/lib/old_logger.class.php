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

   
}
?>