<?php

require_once 'TechDivision/Lang/Object.php';

class TechDivision_AOP_LogTarget extends TechDivision_Lang_Object
{

    protected $_buffer = array();

    public function log($message)
    {
        $this->_buffer[] = $message;
    }

    public function getBuffer()
    {
        return $this->_buffer;
    }

    public function clear()
    {
        $this->_buffer = array();
    }

    public function write()
    {
        $message = implode('', $this->_buffer);
        $this->clear();
        return $message;
    }
}