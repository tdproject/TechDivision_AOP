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

require_once 'TechDivision/Collections/ArrayList.php';
require_once 'TechDivision/AOP/Interfaces/MethodInterceptor.php';
require_once 'TechDivision/AOP/JoinPoint.php';
require_once 'TechDivision/AOP/Aspect/Self.php';
require_once 'TechDivision/AOP/Advice/Helper.php';

/**
 * The method interceptor, necessary to coordinate the Advice handling.
 *
 * @package TechDivision_AOP
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
class TechDivision_AOP_MethodInterceptor
	implements TechDivision_AOP_Interfaces_MethodInterceptor {

	/**
	 * The Aspect Container instance.
	 * @var TechDivision_AOP_Interfaces_AspectContainer
	 */
	protected $_aspectContainer = null;

	/**
	 * The name of the intercepted method.
	 * @var string
	 */
	protected $_method = '';

	/**
	 * Counter for the Advice Chain.
	 * @var integer
	 */
	protected $_nextAdvice = -1;

	/**
	 * The Advice Chain itself.
	 * @var array
	 */
	protected $_adviceChain = array();

	/**
	 * Helper used to temporarily store advices already been added to chain.
	 * @var array
	 */
	protected $_inAdviceChain = array();

	/**
	 * The methods unique signature, used as identifier.
	 * @var string
	 */
	protected $_methodSignature = '';

	/**
	 * Intializes the method interceptor instance.
	 *
	 * @param TechDivision_AOP_Interfaces_AspectContainer $aspectContainer
	 * 		The Aspect Container instance
	 * @param string $method The name of the method to be intercepted
	 * @return void
	 */
	public function __construct(
		TechDivision_AOP_Interfaces_Proxy $proxy,
		$method)
	{
	    // set the Aspect Container and the name of the intercepted method
	    $this->_aspectContainer = $proxy;
		$this->_method = $method;
		// set the method signature from the proxy instance
		$this->_methodSignature = $proxy->getMethodSignature($method);
	}

	/**
	 * Returns the unique method signature.
	 *
	 * @return string
	 */
	public function getMethodSignature()
	{
		return $this->_methodSignature;
	}

	/**
	 * Returns the name of the intercepted method.
	 *
	 * @return string The method name
	 */
	public function getMethod()
	{
		return $this->_method;
	}

	/**
	 * Returns the instance of the aspect container.
	 *
	 * @return TechDivision_AOP_Interfaces_AspectContainer
	 */
	public function getAspectContainer()
	{
	    return $this->_aspectContainer;
	}

	/**
	 * Returns the next Advice from the Advice Chain without
	 * raising the counter by +1.
	 *
	 * @return TechDivision_AOP_Interfaces_Advice The next Advice
	 */
	public function getNextAdvice()
	{
	    // check if another Advice exists
		$nextAdvice = $this->_nextAdvice + 1;
	    if (array_key_exists($nextAdvice, $this->_adviceChain)) {
	        return $this->_adviceChain[$nextAdvice];
	    }
	}

	/**
	 * Proceeds with the next Advice in the Advice Chain and
	 * raises the Advice Chain counter by +1.
	 *
	 * @param TechDivision_AOP_Interfaces_JoinPoint The actual JoinPoint
	 * @return void
	 */
	public function proceed(TechDivision_AOP_Interfaces_JoinPoint $joinPoint)
	{
	    // raise the counter for the next Advice
	    $this->_nextAdvice++;
	    // check if another Advice exists
	    if (array_key_exists($this->_nextAdvice, $this->_adviceChain)) {
	        $nextAdvice = $this->_adviceChain[$this->_nextAdvice];
	        $nextAdvice->invoke($joinPoint);
	    }
	}

	/**
	 * Returns TRUE if at least one of the Pointcuts matches with the
	 * method signature.
	 *
	 * @return boolean TRUE if at least one of the pointcuts, else FALSE
	 */
	public function match()
	{
		// check if pointcuts are available for the method
		$pointcuts = $this->getAspectContainer()->getPointcuts();
		// if not, return immediately
		if ($pointcuts->size() === 0) {
			return false;
		}
        // initialize the array with the Advices
        $adviceChain = array();
	    // load all available Pointcuts
	    foreach ($pointcuts as $pointcut) {
	        // match the regular expression agains the actual method
	        if ($pointcut->match($this)) {
	            // if the regex matches the method, initialize the Advice
	            $advice = TechDivision_AOP_Advice_Helper::create($pointcut);
	            // load the identifier and the order for the Advice
	            $identifier = $advice->getIdentifier();
	            $order = $advice->getOrder();
	            // append the Advice to the Advice Chain
	            $adviceChain[$identifier][$order] = $advice;
	        }
	    }
        // initialize the identifier
        $identifier = TechDivision_AOP_Advice_Around::IDENTIFIER;
        // check if a least one around Advice exists when AdviceChain size > 0
        if (sizeof($adviceChain) > 0 &&
            !array_key_exists($identifier, $adviceChain)) {
            // append the default Pointcut for every method
            $advice = TechDivision_AOP_Advice_Helper::create(
                TechDivision_AOP_Pointcut::create()
                    ->aspect(new TechDivision_AOP_Aspect_Self())
        	        ->intercept(".* .*->{$this->getMethod()}.*(.*)")
        	    	->around()
        	    	->withMethod('__invoke')
        	);
            // add the default Aspect
    	    $adviceChain[$identifier][$order] = $advice;
        }
        // sort the AdviceChain by Before -> Around -> After
	    ksort($adviceChain);
        // merge the AdviceChain
	    $this->merge($adviceChain)->reset();
        // check if at least one advice exists
	    if (sizeof($this->_adviceChain) > 0) {
	        return true;
	    }
	    return false;
	}

	/**
	 * Intercepts the method by checking if at least one Pointcut
	 * exists, that matches the method name. If a match was found
	 * the Advice Chain based on the Pointcut information will be
	 * built, executed and the result returned.
	 *
	 * @param array $arguments The methods arguments
	 * @return mixed The result of the method the aspect relies on,
	 * 		or the result of the executed Advice Chain
	 */
	public function intercept(array $arguments)
	{
	    // create the join point instance
        $joinPoint = TechDivision_AOP_JoinPoint::create()
            ->setMethodInterceptor($this)
            ->setArguments($arguments);
	    // merge and reset the Advice Chain, and proceed with the first one
	    $this->proceed($joinPoint);
	    // return the final result after finishing the Advice Chain
	    return $joinPoint->getResult();
	}

	/**
	 * Merges the muldimensional array with Advices to
	 * an ArraList with all Advices in the necessary
	 * order: Before -> Around -> After
	 *
	 * @param array $advices The array with the Advices
	 * @return TechDivision_AOP_MethodInterceptor The instance itself
	 */
	public function merge($advices) {
	    foreach ($advices as $advice) {
	        if (is_array($advice)) {
	            $this->merge($advice);
	        } else {
				// load the unique name of the advice
	            $uniqueName = $advice->getUniqueName();
				// check if the advice has already been added to the chain
	            if (!in_array($uniqueName, $this->_inAdviceChain)) {
	                $this->_inAdviceChain[] = $uniqueName;
	                $this->_adviceChain[] = $advice;
	            }
	        }
	    }
	    return $this;
	}

	/**
	 * Resets the Advice Chain by set the counter to -1.
	 *
	 * @return TechDivision_AOP_MethodInterceptor The instance itself
	 */
	public function reset()
	{
	    $this->_nextAdvice = -1;
	    return $this;
	}

    /**
     * Returns the Context the Proxy is in.
     *
     * @return TechDivision_Collections_HashMap
     */
    public function getProxyContext()
	{
	    return $this->getAspectContainer()->getProxyContext();
	}
}