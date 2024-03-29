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
 * @author     Sebastian Bergmann <sebastian@phpunit.de>
 * @copyright  2002-2011 Sebastian Bergmann <sebastian@phpunit.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://www.phpunit.de/
 * @since      File available since Release 3.5.0
 */

require_once 'PHPUnit/Util/Filesystem.php';
require_once 'PHP/CodeCoverage/Filter.php';

if (!function_exists('phpunit_autoload')) {
    function phpunit_autoload($class)
    {
        if (strpos($class, 'PHPUnit_') === 0) {
            $file = str_replace('_', '/', $class) . '.php';
            $file = PHPUnit_Util_Filesystem::fileExistsInIncludePath($file);
            if ($file) {
                require_once $file;
            }
        }
    }

    spl_autoload_register('phpunit_autoload');

    $dir    = dirname(__FILE__);
    $filter = PHP_CodeCoverage_Filter::getInstance();

    $filter->addDirectoryToBlacklist(
      $dir . '/Extensions', '.php', '', 'PHPUNIT', FALSE
    );

    $filter->addDirectoryToBlacklist(
      $dir . '/Framework', '.php', '', 'PHPUNIT', FALSE
    );

    $filter->addDirectoryToBlacklist(
      $dir . '/Runner', '.php', '', 'PHPUNIT', FALSE
    );

    $filter->addDirectoryToBlacklist(
      $dir . '/TextUI', '.php', '', 'PHPUNIT', FALSE
    );

    $filter->addDirectoryToBlacklist(
      $dir . '/Util', '.php', '', 'PHPUNIT', FALSE
    );

    $filter->addFileToBlacklist(__FILE__, 'PHPUNIT', FALSE);

    unset($dir, $filter);
}
