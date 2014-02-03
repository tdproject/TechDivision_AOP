<?php

require_once 'TechDivision/AOP/Aspect.php';
require_once 'TechDivision/AOP/Interfaces/JoinPoint.php';

/**
 * @package TechDivision_AOP
 * @author wagnert <t.wagner@techdivision.com>
 */
class TechDivision_AOP_TestAspect4
    extends TechDivision_AOP_Aspect {

    protected $_logTarget;
    
    protected $_name = 'barfoo';

    public function __construct(TechDivision_AOP_LogTarget $logTarget) {
        $this->_logTarget = $logTarget;
    }

	public function log(TechDivision_AOP_Interfaces_JoinPoint $joinPoint)
	{
		$this->_logTarget->log(
		    $joinPoint
		        ->getMethodInterceptor()
		        ->getAspectContainer()
		        ->getAspectable()
		        ->getName()
		);
		
		return $this->_name;
	}
}