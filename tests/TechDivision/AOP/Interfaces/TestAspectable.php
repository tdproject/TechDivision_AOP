<?php

/**
 * @package TechDivision_AOP
 * @author wagnert <t.wagner@techdivision.com>
 */
interface TechDivision_AOP_Interfaces_TestAspectable {

    public function getName();

	public function append($arg1);

	public function withStringArgs($arg1 = 'foo');

	public function withNumericArgs($arg1 = 1, $arg2 = 0.1);

	public function withArrayArgs(array $arg1 = array('1' => 'foo'));

	public function withMixedArgs($arg1 = 1, $arg2 = 0.1, array $arg3 = array(1 => 'foo', 2, 'bar'));
}