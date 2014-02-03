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

require_once 'TechDivision/AOP/Interfaces/Aspect.php';
require_once 'TechDivision/AOP/Interfaces/Pointcut.php';
require_once 'TechDivision/AOP/JoinPoint.php';
require_once 'TechDivision/AOP/Advice/Before.php';
require_once 'TechDivision/AOP/Advice/Around.php';
require_once 'TechDivision/AOP/Advice/After.php';
require_once 'TechDivision/AOP/Advice/Helper.php';

/**
 * A Poincut implementation.
 *
 * @package TechDivision_AOP
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
class TechDivision_AOP_Pointcut implements TechDivision_AOP_Interfaces_Pointcut
{

	/**
	 * The Aspect the pointcut is dedicated to.
	 * @var TechDivision_AOP_Interfaces_Aspect
	 */
    protected $_aspect = null;

    /**
     * The advice the pointcut is dedicated to.
     * @var array
     */
    protected $_advice = array();

    /**
     * The method to be intercepted.
     * @var string
     */
	protected $_methodToIntercept = '';

	/**
	 * The method to intercept with.
	 * @var string
	 */
	protected $_interceptWithMethod = '';

	/**
	 * The method signature elements.
	 * @var array
	 */
	protected $_elements = array('modifier', 'class', 'method', 'args');

	/**
	 * Matches for the message signature.
	 * @var unknown_type
	 */
	protected $_matches = array();

	/**
	 * The regular expression necessary to match the method to intercept with.
	 * @var string
	 */
    const PATTERNMATCHMETHOD = '/^(?P<modifier>.*) (?P<class>.*)->(?P<method>.*)\((?P<args>.*)\)$/';

    /**
     * Factory method to create a new pointcut instance.
     *
     * @return TechDivision_AOP_Pointcut
     * 		The instance itself
     */
	public static function create()
	{
	    return new TechDivision_AOP_Pointcut();
	}

	/**
	 * Sets the aspect the pointcut is dedicated to.
	 *
	 * @param TechDivision_AOP_Interfaces_Aspect $aspect
	 * 		The aspect the pointcut is dedicated to
	 * @return TechDivision_AOP_Pointcut
	 * 		The instance itself
	 */
	public function aspect(TechDivision_AOP_Interfaces_Aspect $aspect)
	{
	    $this->_aspect = $aspect;
	    return $this;
	}

	/**
	 * Sets the method to be intercepted with this pointcut.
	 *
	 * @param string $methodToIntercept The method name to intercept
	 * @return TechDivision_AOP_Pointcut
	 * 		The instance itself
	 */
	public function intercept($methodToIntercept)
	{
	    $this->_methodToIntercept = $methodToIntercept;
	    return $this;
	}

	/**
	 * Sets the advice the pointcut is decidated to.
	 *
	 * @param string $advice The advice (before, around or after)
	 * @param integer $order The order of the advice to use
	 * @throws Exception Is thrown if an invalid advice is passed
	 * @return TechDivision_AOP_Pointcut
	 * 		The instance itself
	 */
	public function setAdvice($advice, $order)
	{
		// check if a valid advice has been passed
        if (TechDivision_AOP_Advice_Helper::isValidAdvice($advice) === false) {
            throw new Exception("Invalid advice $advice given");
        }
		// append the advice to
        $this->_advice = array($order, $advice);
        return $this;
	}

	/**
	 * Wrapper method to add set the pointcut as after advice.
	 *
	 * @param integer $order The order of the advice to use
	 * @return TechDivision_AOP_Pointcut
	 * 		The instance itself
	 */
	public function after($order = 0)
	{
	    return $this->setAdvice(
	        TechDivision_AOP_Advice_After::IDENTIFIER,
	        $order
	    );
	}

	/**
	 * Wrapper method to add set the pointcut as before advice.
	 *
	 * @param integer $order The order of the advice to use
	 * @return TechDivision_AOP_Pointcut
	 * 		The instance itself
	 */
	public function before($order = 0)
	{
	    return $this->setAdvice(
	        TechDivision_AOP_Advice_Before::IDENTIFIER,
	        $order
	    );
	}

	/**
	 * Wrapper method to add set the pointcut as around advice.
	 *
	 * @param integer $order The order of the advice to use
	 * @return TechDivision_AOP_Pointcut
	 * 		The instance itself
	 */
	public function around($order = 0)
	{
	    return $this->setAdvice(
	        TechDivision_AOP_Advice_Around::IDENTIFIER,
	        $order
	    );
	}

	/**
	 * The method name to intercept with.
	 *
	 * @param string $interceptWithMethod
	 * 		The name of the method to intercept with
	 * @return TechDivision_AOP_Pointcut
	 * 		The instance itself
	 */
	public function withMethod($interceptWithMethod)
	{
	    $this->_interceptWithMethod = $interceptWithMethod;
	    return $this;
	}

	/**
	 * Returns the method to be intercepted with this pointcut.
	 *
	 * @return string The method name
	 */
	public function getMethodToIntercept()
	{
		return $this->_methodToIntercept;
	}

	/**
	 * Returns the method name to intercept with.
	 *
	 * @return string The method name
	 */
	public function interceptWithMethod()
	{
		// check if a method is set, else use the name of the method to intercept
		if (empty($this->_interceptWithMethod)) {
			return $this->getMethodToIntercept();
		}
		// return the method name
		return $this->_interceptWithMethod;
	}

	/**
	 * Returns the aspect the pointcut is dedicated to.
	 *
	 * @return TechDivision_AOP_Interfaces_Aspect
	 * 		The aspect itself
	 */
	public function getAspect()
	{
		return $this->_aspect;
	}

	/**
	 * Returns the advice the pointcut is bound to.
	 *
	 * @return array The advice
	 */
	public function getAdvice()
	{
	    return $this->_advice;
	}

	/**
	 * Adds a flag for the passed hash that represents that a method
	 * signature has already been matched.
	 *
	 * @param string $hash Hash representation of a method
	 * 		signature and the anme of method to intercept
	 * @param boolean $match The flag itself
	 * @return boolean The flag passed as parameter
	 */
	public function addMatch($hash, $match = false)
	{
		return $this->_matches[$hash] = $match;
	}

	/**
	 * Checks if a match for the passed has is already available.
	 *
	 * @param string $hash The hash to check
	 * @return boolean TRUE if a match is available, else FALSE
	 */
	public function testMatch($hash)
	{
		return array_key_exists($hash, $this->_matches);
	}

	/**
	 * Returns the match flag for the passed hash.
	 *
	 * @param string $hash The hash to return the match for
	 * @return boolean TRUE if a valid match is available
	 */
	public function getMatch($hash)
	{
		if (array_key_exists($hash, $this->_matches)) {
			return $this->_matches[$hash];
		}
	}

	/**
	 * Matches the method signature of the passed method interceptor with the
	 * pointcut regex for method to intercept.
	 *
	 * @param TechDivision_AOP_Interfaces_MethodInterceptor $methodInterceptor
	 * 		The method interceptor with the method signature to match
	 * @return boolean TRUE if the method signature match the pointcut, else FALSE
	 */
	public function match(
	    TechDivision_AOP_Interfaces_MethodInterceptor $methodInterceptor)
	{
		// load method signature from passed method interceptor -> method called
	    $methodSignature = $methodInterceptor->getMethodSignature();
	    // load method to intercept -> method this pointcut is bound to
        $methodToIntercept = $this->getMethodToIntercept();
		// create a hash value to identified methods already has been matched
        $hash = md5($methodSignature . $methodToIntercept);
		// check if a match result is available
        if ($this->testMatch($hash)) {
        	// if yes, return the result
        	return $this->getMatch($hash);
        }
		// check if method the method to be intercepted matches the regex
        if (!preg_match(self::PATTERNMATCHMETHOD, $methodToIntercept, $patterns)) {
			return $this->addMatch($hash, false);
        }
		// if it does, split the passed method signature
        preg_match(self::PATTERNMATCHMETHOD, $methodSignature, $subjects);
		// iterate over the found elements
        for ($i = 0; $i < sizeof($this->_elements); $i++) {
			// load the element key (modifier, class, method or args)
            $key = $this->_elements[$i];
			// initialize the patter and the subject
            $pattern = "/^" . $patterns[$key] . "$/";
            $subject = $subjects[$key];
			// check if the pattern matches
            if (!preg_match($pattern, $subject)) {
				return $this->addMatch($hash, false);
            }
        }
		// return TRUE if all of the elements match the method signature
        return $this->addMatch($hash, true);
	}

	/**
	 * Returns the pointcut representation a string (uses the name of
	 * the method to intercept by default).
	 *
	 * @return string The pointcut name
	 */
	public function __toString()
	{
	    return $this->_methodToIntercept;
	}
}