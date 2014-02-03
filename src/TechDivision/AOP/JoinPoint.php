<?php

/**
 * License: GNU General Public License
 *
 * Copyright (c) 2009 TechDivision GmbH.  All rights reserved.
 * Note: Original work copyright to respective authors
 *
 * This file is part of TechDivision GmbH - Connect.
 *
 * TechDivision_Lang is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * TechDivision_Lang is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
 * USA.
 *
 * @package TechDivision_AOP
 */

require_once 'TechDivision/Lang/Object.php';
require_once 'TechDivision/AOP/Interfaces/JoinPoint.php';
require_once 'TechDivision/AOP/Interfaces/MethodInterceptor.php';

/**
 * @package TechDivision_AOP
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
class TechDivision_AOP_JoinPoint
    extends TechDivision_Lang_Object
    implements TechDivision_AOP_Interfaces_JoinPoint {

	/**
	 * The method's result.
	 * @var mixed
	 */
    protected $_result = null;

    /**
     * The method's arguments.
     * @var array
     */
    protected $_arguments = array();

    /**
     * The method interceptor to use.
     * @var TechDivision_AOP_Interfaces_MethodInterceptor
     */
    protected $_methodInterceptor = null;

    /**
     * Factory method to create a new join point instance.
     *
     * @return TechDivision_AOP_JoinPoint
     */
    public static function create()
    {
        return new TechDivision_AOP_JoinPoint();
    }

    /**
     * Sets the actual method interceptor.
     *
     * @param TechDivision_AOP_Interfaces_MethodInterceptor $methodInterceptor
     * @return TechDivision_AOP_JoinPoint The instance itself
     */
    public function setMethodInterceptor(
        TechDivision_AOP_Interfaces_MethodInterceptor $methodInterceptor) {
        $this->_methodInterceptor = $methodInterceptor;
        return $this;
    }

    /**
     * Returns the actual method interceptor.
     *
     * @return TechDivision_AOP_Interfaces_MethodInterceptor
     * 		The method interceptor itself
     */
    public function getMethodInterceptor()
    {
        return $this->_methodInterceptor;
    }

    /**
     * Sets the method's arguments.
     *
     * @param array $arguments The method's arguments
     * @return TechDivision_AOP_JoinPoint The instance itself
     */
    public function setArguments(array $arguments)
    {
        $this->_arguments = $arguments;
        return $this;
    }

    /**
     * Returns the method's arguments.
     *
     * @return array The method's arguments
     */
    public function getArguments()
    {
        return $this->_arguments;
    }

    /**
     * Sets the method's result.
     *
     * @param mixed $result The method's result
     * @return TechDivision_AOP_JoinPoint The instance itself
     */
    public function setResult($result)
    {
        $this->_result = $result;
        return $this;
    }

    /**
     * Returns the method's result.
     *
     * @return mixed The method's result
     */
    public function getResult()
    {
        return $this->_result;
    }

    /**
     * Returns the Context the Proxy is in.
     *
     * @return TechDivision_Collections_HashMap
     */
    public function getProxyContext()
    {
        return $this->getMethodInterceptor()->getProxyContext();
    }

    /**
     * To invoke the method the Aspect relies on this method checks
     * if the next Advice is of type Around. If so, it proceeds with
     * the next Advice, as long as an Around Advice follows. If not
     * the method the Aspect relies on will be executed and it's
     * result will be returned.
     *
     * This method has to be called from each Around Advice, if not
     * executions of Around Advices will be broken (maybe this will
     * be the preferred way, e. g. to implement caching functionality.
     *
     * @return mixed The result of the next Around Advice or the value of the
     * 		method the Aspect relies on
     */
    public function proceed()
    {
		// invoke the next Advice in the chain
		$nextAdvice = $this->getMethodInterceptor()->getNextAdvice();
		// check if another Around Advice follows
		if ($nextAdvice instanceof TechDivision_AOP_Advice_Around) {
    		// if yes, invoke it and return the value
		    return $this->getMethodInterceptor()->proceed($this);
		}
        // if not, load the instance to be aspected
	    $aspectable = $this
	        ->getMethodInterceptor()
	        ->getAspectContainer()
	        ->getAspectable();
		// prepare the name of the method to intercept
		$methodToIntercept = "___" . $this->getMethodInterceptor()->getMethod();
		// invoke the method to intercept and return the result
	    return $aspectable->$methodToIntercept($this->getArguments());
    }
}