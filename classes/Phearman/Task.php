<?php

namespace Phearman;
use Phearman\Exception;

/**
 * Abstract Task class. Task classes that encapsulates the differet Gearman
 * job server packets must inherit the abstract Task class.
 *
 * Provides a factory method to easily create a task for a specific packet,
 * serialize a task to a packet by the use of the magic method __toString(),
 * etc.
 *
 * @abstract
 * @author Inash Zubair <inash@leptone.com>
 * @package Phearman
 * @license http://www.opensource.org/licenses/BSD-3-Clause
 */
abstract class Task
{
    protected $code;
    protected $type;
    protected $length;

    final public static function factory($name, $type)
    {
        $class     = new \ReflectionClass('Phearman\Phearman');
        $constants = $class->getConstants();
        $name      = array_search($name, $constants);
        $type      = strtoupper($type);

        if (!$name)
            throw new Exception('Invalid Phearman Task type.');

        /* Throw exception if the task type is is not REQ or RES. */
        if (!in_array($type, array(
        Phearman::CODE_REQUEST,
        Phearman::CODE_RESPONSE))) {
            throw new Exception('The Task Type should either be REQ or RES.');
        }

        /* Transpose type to full form for directory access. */
        if ($type == Phearman::CODE_REQUEST)  $type = 'Request';
        if ($type == Phearman::CODE_RESPONSE) $type = 'Response';

        $className = ucwords(strtolower(str_replace('_', ' ', str_replace('TYPE_', '', $name))));
        $className = str_replace(' ', '', $className);
        $className = "Phearman\\Task\\$type\\$className";

        return new $className;
    }

    protected function getDataPart() {}

    protected function setFromResponse() {}

    public function getTypeName()
    {
        $class = new \ReflectionClass('Phearman\\Phearman');
        $constants = $class->getConstants();

        $type = array_search($this->getType(), $constants);
        $type = str_replace('TYPE_', '', $type);
        return $type;
    }

    final public function __toString()
    {
        /* Prepare data part. */
        $dataPart = $this->getDataPart();

        /* Check if dataPart is returned as an array and if it needs to be
         * joined using the null byte. */
        if (is_array($dataPart) && count($dataPart) > 0)
            $dataPart = join("\0", $dataPart);

        /* Prepare workload together with data part. */
        $this->length = strlen($dataPart);

        /* Prepare header. */
        $code   = "\0" . trim($this->code);
        $header = pack('NN', $this->type, $this->length);
        $task   = $code . $header . $dataPart;

        return $task;
    }

    /**
     * Magic call method to emulate setters and getters for class variables.
     *
     * @access public
     * @param string $variable
     * @param mixed $value
     */
    final public function __call($method, $arguments)
    {
        /* Check if method is prefixed with set or get. */
        if (!in_array(strtolower(substr($method, 0, 3)), array('set', 'get'))) {
            $className = get_class($this);
            throw new Exception(
                "Method {$className}::{$method} does not exist.");
        }

        /* Transpose method to variable name. */
        $variable = lcfirst(substr($method, 3));

        /* Check if private class variable $variable is defined for the current
         * derived class. */
        $class = new \ReflectionClass(get_class($this));

        try {
            $property = $class->getProperty($variable);
        } catch (\ReflectionException $e) {
            $className = get_class($this);
            throw new Exception(
                "Method {$className}::{$method} does not exist.");
        }

        /* Perform get or set based on the method prefix. */
        $prefix = strtolower(substr($method, 0, 3));
        switch ($prefix) {
            case 'get':
                return $this->$variable;
                break;

            case 'set':
                $this->$variable = $arguments[0];
                return $this;
                break;
        }
    }
}
