<?php
/**
 * PHPUnit
 *
 * Copyright (c) 2002-2011, Sebastian Bergmann <sebastian@phpunit.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    PHPUnit
 * @subpackage Util
 * @author     Sebastian Bergmann <sebastian@phpunit.de>
 * @copyright  2002-2011 Sebastian Bergmann <sebastian@phpunit.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://www.phpunit.de/
 * @since      File available since Release 2.3.0
 */

/**
 * Utility methods to load PHP sourcefiles.
 *
 * @package    PHPUnit
 * @subpackage Util
 * @author     Sebastian Bergmann <sebastian@phpunit.de>
 * @copyright  2002-2011 Sebastian Bergmann <sebastian@phpunit.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: 3.5.9
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 2.3.0
 */
class PHPUnit_Util_Fileloader
{
    /**
     * Checks if a PHP sourcefile is readable and is optionally checked for
     * syntax errors through the syntaxCheck() method. The sourcefile is
     * loaded through the load() method.
     *
     * @param  string  $filename
     * @param  boolean $syntaxCheck
     * @throws RuntimeException
     */
    public static function checkAndLoad($filename, $syntaxCheck = FALSE)
    {
        $includePathFilename = PHPUnit_Util_Filesystem::fileExistsInIncludePath(
          $filename
        );

        if (!$includePathFilename || !is_readable($includePathFilename)) {
            throw new RuntimeException(
              sprintf('Cannot open file "%s".' . "\n", $filename)
            );
        }

        if ($syntaxCheck) {
            self::syntaxCheck($includePathFilename);
        }

        self::load($includePathFilename);
    }

    /**
     * Loads a PHP sourcefile.
     *
     * @param  string $filename
     * @return mixed
     * @since  Method available since Release 3.0.0
     */
    public static function load($filename)
    {
        $oldVariableNames = array_keys(get_defined_vars());

        include_once $filename;

        $newVariables     = get_defined_vars();
        $newVariableNames = array_diff(
                              array_keys($newVariables), $oldVariableNames
                            );

        foreach ($newVariableNames as $variableName) {
            if ($variableName != 'oldVariableNames') {
                $GLOBALS[$variableName] = $newVariables[$variableName];
            }
        }

        return $filename;
    }

    /**
     * Uses a separate process to perform a syntax check on a PHP sourcefile.
     *
     * @param  string $filename
     * @throws RuntimeException
     * @since  Method available since Release 3.0.0
     */
    protected static function syntaxCheck($filename)
    {
        $command = PHPUnit_Util_PHP::getPhpBinary();

        if (DIRECTORY_SEPARATOR == '\\') {
            $command = escapeshellarg($command);
        }

        $command .= ' -l ' . escapeshellarg($filename);
        $output   = shell_exec($command);

        if (strpos($output, 'Errors parsing') !== FALSE) {
            throw new RuntimeException($output);
        }
    }
}
