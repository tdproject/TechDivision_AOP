<?php

require_once 'TechDivision/AOP/Aspect.php';
require_once 'TechDivision/AOP/Interfaces/JoinPoint.php';

/**
 * @package TechDivision_AOP
 * @author wagnert <t.wagner@techdivision.com>
 */
class TechDivision_AOP_TestAspect
    extends TechDivision_AOP_Aspect {

	public function failWithException(
	    TechDivision_AOP_Interfaces_JoinPoint $joinPoint) {
		throw new Exception('No Reason');
	}
}