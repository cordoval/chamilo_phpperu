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
 * @since      File available since Release 3.1.0
 */

if (!defined('T_NAMESPACE')) {
    define('T_NAMESPACE', 377);
}

/**
 * Class helpers.
 *
 * @package    PHPUnit
 * @subpackage Util
 * @author     Sebastian Bergmann <sebastian@phpunit.de>
 * @copyright  2002-2011 Sebastian Bergmann <sebastian@phpunit.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: 3.5.9
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 3.1.0
 */
class PHPUnit_Util_Class
{
    protected static $buffer = array();

    /**
     * Starts the collection of loaded classes.
     *
     */
    public static function collectStart()
    {
        self::$buffer = get_declared_classes();
    }

    /**
     * Stops the collection of loaded classes and
     * returns the names of the loaded classes.
     *
     * @return array
     */
    public static function collectEnd()
    {
        return array_values(
          array_diff(get_declared_classes(), self::$buffer)
        );
    }

    /**
     * Returns the class hierarchy for a given class.
     *
     * @param  string  $className
     * @param  boolean $asReflectionObjects
     * @return array
     */
    public static function getHierarchy($className, $asReflectionObjects = FALSE)
    {
        if ($asReflectionObjects) {
            $classes = array(new ReflectionClass($className));
        } else {
            $classes = array($className);
        }

        $done = FALSE;

        while (!$done) {
            if ($asReflectionObjects) {
                $class = new ReflectionClass(
                  $classes[count($classes)-1]->getName()
                );
            } else {
                $class = new ReflectionClass($classes[count($classes)-1]);
            }

            $parent = $class->getParentClass();

            if ($parent !== FALSE) {
                if ($asReflectionObjects) {
                    $classes[] = $parent;
                } else {
                    $classes[] = $parent->getName();
                }
            } else {
                $done = TRUE;
            }
        }

        return $classes;
    }

    /**
     * Returns the parameters of a function or method.
     *
     * @param  ReflectionFunction|ReflectionMethod $method
     * @param  boolean                             $forCall
     * @return string
     * @since  Method available since Release 3.2.0
     */
    public static function getMethodParameters($method, $forCall = FALSE)
    {
        $parameters = array();

        foreach ($method->getParameters() as $i => $parameter) {
            $name = '$' . $parameter->getName();

            if ($name === '$') {
                $name .= 'arg' . $i;
            }

            $default  = '';
            $typeHint = '';

            if (!$forCall) {
                if ($parameter->isArray()) {
                    $typeHint = 'array ';
                } else {
                    try {
                        $class = $parameter->getClass();
                    }

                    catch (ReflectionException $e) {
                        $class = FALSE;
                    }

                    if ($class) {
                        $typeHint = $class->getName() . ' ';
                    }
                }

                if ($parameter->isDefaultValueAvailable()) {
                    $value   = $parameter->getDefaultValue();
                    $default = ' = ' . var_export($value, TRUE);
                }

                else if ($parameter->isOptional()) {
                    $default = ' = null';
                }
            }

            $ref = '';

            if ($parameter->isPassedByReference()) {
                $ref = '&';
            }

            $parameters[] = $typeHint . $ref . $name . $default;
        }

        return join(', ', $parameters);
    }

    /**
     * Returns the package information of a user-defined class.
     *
     * @param  string $className
     * @param  string $docComment
     * @return array
     */
    public static function getPackageInformation($className, $docComment)
    {
        $result = array(
          'namespace'   => '',
          'fullPackage' => '',
          'category'    => '',
          'package'     => '',
          'subpackage'  => ''
        );

        if (strpos($className, '\\') !== FALSE) {
            $result['namespace'] = self::arrayToName(
              explode('\\', $className)
            );
        }

        if (preg_match('/@category[\s]+([\.\w]+)/', $docComment, $matches)) {
            $result['category'] = $matches[1];
        }

        if (preg_match('/@package[\s]+([\.\w]+)/', $docComment, $matches)) {
            $result['package']     = $matches[1];
            $result['fullPackage'] = $matches[1];
        }

        if (preg_match('/@subpackage[\s]+([\.\w]+)/', $docComment, $matches)) {
            $result['subpackage']   = $matches[1];
            $result['fullPackage'] .= '.' . $matches[1];
        }

        if (empty($result['fullPackage'])) {
            $result['fullPackage'] = self::arrayToName(
              explode('_', str_replace('\\', '_', $className)), '.'
            );
        }

        return $result;
    }

    /**
     * Returns the value of a static attribute.
     * This also works for attributes that are declared protected or private.
     *
     * @param  string  $className
     * @param  string  $attributeName
     * @return mixed
     * @throws InvalidArgumentException
     * @since  Method available since Release 3.4.0
     */
    public static function getStaticAttribute($className, $attributeName)
    {
        if (!is_string($className)) {
            throw PHPUnit_Util_InvalidArgumentHelper::factory(1, 'string');
        }

        if (!class_exists($className)) {
            throw PHPUnit_Util_InvalidArgumentHelper::factory(1, 'class name');
        }

        if (!is_string($attributeName)) {
            throw PHPUnit_Util_InvalidArgumentHelper::factory(2, 'string');
        }

        $class = new ReflectionClass($className);

        while ($class) {
            $attributes = $class->getStaticProperties();

            if (array_key_exists($attributeName, $attributes)) {
                return $attributes[$attributeName];
            }

            $class = $class->getParentClass();
        }

        throw new PHPUnit_Framework_Exception(
          sprintf(
            'Attribute "%s" not found in class.',

            $attributeName
          )
        );
    }

    /**
     * Returns the value of an object's attribute.
     * This also works for attributes that are declared protected or private.
     *
     * @param  object  $object
     * @param  string  $attributeName
     * @return mixed
     * @throws InvalidArgumentException
     * @since  Method available since Release 3.4.0
     */
    public static function getObjectAttribute($object, $attributeName)
    {
        if (!is_object($object)) {
            throw PHPUnit_Util_InvalidArgumentHelper::factory(1, 'object');
        }

        if (!is_string($attributeName)) {
            throw PHPUnit_Util_InvalidArgumentHelper::factory(2, 'string');
        }

        PHPUnit_Framework_Assert::assertObjectHasAttribute(
          $attributeName, $object
        );

        try {
            $attribute = new ReflectionProperty($object, $attributeName);
        }

        catch (ReflectionException $e) {
            $reflector = new ReflectionObject($object);

            while ($reflector = $reflector->getParentClass()) {
                try {
                    $attribute = $reflector->getProperty($attributeName);
                    break;
                }

                catch(ReflectionException $e) {
                }
            }
        }

        if ($attribute == NULL || $attribute->isPublic()) {
            return $object->$attributeName;
        } else {
            $array         = (array)$object;
            $protectedName = "\0*\0" . $attributeName;

            if (array_key_exists($protectedName, $array)) {
                return $array[$protectedName];
            } else {
                $classes = self::getHierarchy(get_class($object));

                foreach ($classes as $class) {
                    $privateName = sprintf(
                      "\0%s\0%s",

                      $class,
                      $attributeName
                    );

                    if (array_key_exists($privateName, $array)) {
                        return $array[$privateName];
                    }
                }
            }
        }

        throw new PHPUnit_Framework_Exception(
          sprintf(
            'Attribute "%s" not found in object.',

            $attributeName
          )
        );
    }

    /**
     *
     *
     * @param  string $className
     * @return array
     * @since  Method available since Release 3.4.0
     */
    public static function parseFullyQualifiedClassName($className)
    {
        $result = array(
          'namespace'               => '',
          'className'               => $className,
          'fullyQualifiedClassName' => $className
        );

        if (strpos($className, '\\') !== FALSE) {
            $tmp                 = explode('\\', $className);
            $result['className'] = $tmp[count($tmp)-1];
            $result['namespace'] = self::arrayToName($tmp);
        }

        return $result;
    }

    /**
     * Returns the package information of a user-defined class.
     *
     * @param  array  $parts
     * @param  string $join
     * @return string
     * @since  Method available since Release 3.2.12
     */
    protected static function arrayToName(array $parts, $join = '\\')
    {
        $result = '';

        if (count($parts) > 1) {
            array_pop($parts);

            $result = join($join, $parts);
        }

        return $result;
    }
}
