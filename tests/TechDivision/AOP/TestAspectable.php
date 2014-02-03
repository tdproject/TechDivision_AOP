<?php

require_once 'TechDivision/Lang/Object.php';
require_once 'TechDivision/AOP/Interfaces/TestAspectable.php';

/**
 * @package TechDivision_AOP
 * @author wagnert <t.wagner@techdivision.com>
 */
class TechDivision_AOP_TestAspectable
    extends TechDivision_Lang_Object
    implements TechDivision_AOP_Interfaces_TestAspectable {

    protected $_name = '';

    public function __construct($name)
    {
        $this->_name = $name;
    }

    public function getName()
    {
        return $this->_name;
    }

	public function append($arg1)
	{
		return $this->_name .= $arg1;
	}

	public function withStringArgs($arg1 = 'foo')
	{
		return $this->_name .= $arg1;
	}

	public function withNumericArgs($arg1 = 1, $arg2 = 0.1)
	{
		return $this->_name .= $arg1;
	}

	public function withArrayArgs(array $arg1 = array('1' => 'foo'))
	{
		return $this->_name .= implode(', ', $arg1);
	}

	public function withMixedArgs($arg1 = 1, $arg2 = 0.1, array $arg3 = array(1 => 'foo', 2, 'bar'))
	{
		return $this->_name .= $arg1;
	}
}