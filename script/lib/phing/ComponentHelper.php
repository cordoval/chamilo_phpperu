<?php
/*
 *  $Id: ComponentHelper.php 987 2010-11-12 14:27:54Z mrook $
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information please see
 * <http://phing.info>.
 */

include_once 'phing/system/io/PhingFile.php';
include_once 'phing/util/FileUtils.php';
include_once 'phing/TaskAdapter.php';
include_once 'phing/util/StringHelper.php';
include_once 'phing/BuildEvent.php';
include_once 'phing/input/DefaultInputHandler.php';

/**
 *  The Phing project class. Represents a completely configured Phing project.
 *  The class defines the project and all tasks/targets. It also contains
 *  methods to start a build as well as some properties and FileSystem
 *  abstraction.
 *
 * @author    Andreas Aderhold <andi@binarycloud.com>
 * @author    Hans Lellelid <hans@xmpl.org>
 * @version   $Revision: 987 $
 * @package   phing
 */
class ComponentHelper {
    const COMPONENT_HELPER_REFERENCE = "phing.ComponentHelper";
    
    /**
     * Map of task definitions
     */
    private $taskdefs = array();
    
    /**
     * Map of type definitions
     */
    private $typedefs = array();
    
    /**
     * project owning the component helper
     */
    private $project = null;

    /**
     *  Constructor, sets any default vars.
     */
    protected function __construct()
    {
    }
    
    /**
     * Setter for project variable
     */
    public function setProject($project)
    {
        $this->project = $project;
    }
    
    /**
     * Getter for project variable
     */
    public function getProject()
    {
        return $this->project;
    }
    
    /**
     * Retrieves the instance of ComponentHelper for this project
     */
    public static function getComponentHelper(Project $project)
    {
        if ($project === null) {
            return null;
        }
        
        $ph = $project->getReference(self::COMPONENT_HELPER_REFERENCE);
        
        if ($ph !== null) {
            return $ph;
        }
        
        $ph = new ComponentHelper();
        $ph->setProject($project);
        
        $project->addReference(self::COMPONENT_HELPER_REFERENCE, $ph);
        
        return $ph;
    }

    /**
     * Adds a task definition.
     * @param string $name Name of tag.
     * @param string $class The class path to use.
     * @param string $classpath The classpat to use.
     */
    public function addTaskDefinition($name, $class, $classpath = null)
    {
        $name  = $name;
        $class = $class;
        if ($class === "") {
            $this->project->log("Task $name has no class defined.", Project::MSG_ERR);
        }  elseif (!isset($this->taskdefs[$name])) {
            $this->taskdefs[$name] = $class;
            $this->project->log("  +Task definiton: $name ($class)", Project::MSG_DEBUG);
        } else {
            $this->project->log("Task $name ($class) already registerd, skipping", Project::MSG_VERBOSE);
        }
    }

    /**
     * Return the map of tasks
     */
    public function getTaskDefinitions()
    {
        return $this->taskdefs;
    }
    
    /**
     * Import/load class
     */
    public function checkTaskClass($name)
    {
        $classname = $this->taskdefs[$name];
        
        return Phing::import($classname);
    }

    /**
     * Adds a data type definition.
     * @param string $name Name of tag.
     * @param string $class The class path to use.
     * @param string $classpath The classpat to use.
     */
    public function addDataTypeDefinition($typeName, $typeClass, $classpath = null)
    {
        if (!isset($this->typedefs[$typeName])) {
            Phing::import($typeClass, $classpath);
            $this->typedefs[$typeName] = $typeClass;
            $this->project->log("  +User datatype: $typeName ($typeClass)", Project::MSG_DEBUG);
        } else {
            $this->project->log("Type $typeName ($typeClass) already registerd, skipping", Project::MSG_VERBOSE);
        }
    }

    /**
     * Return the map of data types
     */
    public function getDataTypeDefinitions()
    {
        return $this->typedefs;
    }

    /**
     * Create a new task instance and return reference to it. This method is
     * sorta factory like. A _local_ instance is created and a reference returned to
     * that instance. Usually PHP destroys local variables when the function call
     * ends. But not if you return a reference to that variable.
     * This is kinda error prone, because if no reference exists to the variable
     * it is destroyed just like leaving the local scope with primitive vars. There's no
     * central place where the instance is stored as in other OOP like languages.
     *
     * [HL] Well, ZE2 is here now, and this is  still working. We'll leave this alone
     * unless there's any good reason not to.
     *
     * @param    string    $taskType    Task name
     * @returns  Task                A task object
     * @throws   BuildException
     *           Exception
     */
    public function createTask($taskType)
    {
        try {
            $classname = "";
            $tasklwr = strtolower($taskType);
            foreach ($this->taskdefs as $name => $class) {
                if (strtolower($name) === $tasklwr) {
                    $classname = $class;
                    break;
                }
            }
            
            if ($classname === "") {
                return null;
            }
            
            $cls = Phing::import($classname);
            
            if (!class_exists($cls)) {
                throw new BuildException("Could not instantiate class $cls, even though a class was specified. (Make sure that the specified class file contains a class with the correct name.)");
            }
            
            $o = new $cls();
    
            if ($o instanceof Task) {
                $task = $o;
            } else {
                $this->project->log("  (Using TaskAdapter for: $taskType)", Project::MSG_DEBUG);
                // not a real task, try adapter
                $taskA = new TaskAdapter();
                $taskA->setProxy($o);
                $task = $taskA;
            }
            $task->setProject($this->project);
            $task->setTaskType($taskType);
            // set default value, can be changed by the user
            $task->setTaskName($taskType);
            $this->project->log ("  +Task: " . $taskType, Project::MSG_DEBUG);
        } catch (Exception $t) {
            throw new BuildException("Could not create task of type: " . $taskType, $t);
        }
        // everything fine return reference
        return $task;
    }

    /**
     * Create a datatype instance and return reference to it
     * See createTask() for explanation how this works
     *
     * @param    string   Type name
     * @returns  object   A datatype object
     * @throws   BuildException
     *           Exception
     */
    public function createDataType($typeName)
    {
        try {
            $cls = "";
            $typelwr = strtolower($typeName);
            foreach ($this->typedefs as $name => $class) {
                if (strtolower($name) === $typelwr) {
                    $cls = StringHelper::unqualify($class);
                    break;
                }
            }
            
            if ($cls === "") {
                return null;
            }
            
            if (!class_exists($cls)) {
                throw new BuildException("Could not instantiate class $cls, even though a class was specified. (Make sure that the specified class file contains a class with the correct name.)");
            }
            
            $type = new $cls();
            $this->project->log("  +Type: $typeName", Project::MSG_DEBUG);
            if (!($type instanceof DataType)) {
                throw new Exception("$class is not an instance of phing.types.DataType");
            }
            if ($type instanceof ProjectComponent) {
                $type->setProject($this->project);
            }
        } catch (Exception $t) {
            throw new BuildException("Could not create type: $typeName", $t);
        }
        // everything fine return reference
        return $type;
    }
    
    /**
     * Init default tasks & types
     */
    public function initDefaults()
    {
        $this->initDefaultTasks();
        $this->initDefaultTypes();
    }

    protected function initDefaultTasks()
    {
        // load default tasks
        $taskdefs = Phing::getResourcePath("phing/tasks/defaults.properties");

        try { // try to load taskdefs
            $props = new Properties();
            $in = new PhingFile((string)$taskdefs);

            if ($in === null) {
                throw new BuildException("Can't load default task list");
            }
            $props->load($in);

            $enum = $props->propertyNames();
            foreach($enum as $key) {
                $value = $props->getProperty($key);
                $this->addTaskDefinition($key, $value);
            }
        } catch (IOException $ioe) {
            throw new BuildException("Can't load default task list");
        }
    }
    
    protected function initDefaultTypes()
    {
        // load default types
        $typedefs = Phing::getResourcePath("phing/types/defaults.properties");

        try { // try to load typedefs
            $props = new Properties();
            $in    = new PhingFile((string)$typedefs);
            if ($in === null) {
                throw new BuildException("Can't load default datatype list");
            }
            $props->load($in);

            $enum = $props->propertyNames();
            foreach($enum as $key) {
                $value = $props->getProperty($key);
                $this->addDataTypeDefinition($key, $value);
            }
        } catch(IOException $ioe) {
            throw new BuildException("Can't load default datatype list");
        }
    }
}
