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
require_once 'TechDivision/AOP/Interfaces/Advice.php';
require_once 'TechDivision/AOP/Interfaces/Pointcut.php';
require_once 'TechDivision/AOP/Interfaces/JoinPoint.php';

/**
 * @package TechDivision_AOP
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
abstract class TechDivision_AOP_Advice_Abstract
    extends TechDivision_Lang_Object
    implements TechDivision_AOP_Interfaces_Advice {

	/**
	 * The order to use.
	 * @return integer
	 */
    protected $_order = 0;

    /**
     * The pointcut to use.
     * @var TechDivision_AOP_Interfaces_Pointcut
     */
    protected $_pointcut = null;

    /**
     * Initializes the advice.
     *
     * @param TechDivision_AOP_Interfaces_Pointcut $pointcut
     * 		The pointcut to use
     * @param integer $order The order to use
     */
    public function __construct(
        TechDivision_AOP_Interfaces_Pointcut $pointcut, $order) {
        $this->_pointcut = $pointcut;
        $this->_order = $order;
    }

    /**
     * Returns the advice order.
     *
     * @return integer The advice number
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * Returns the pointcut.
     *
     * @return TechDivision_AOP_Interfaces_JoinPoint
     * 		The pointcut instance
     */
    public function getPointcut()
    {
        return $this->_pointcut;
    }

    /**
     * Abstract method to request identifier implementation.
     *
     * @return integer The unique identifier (before = 1, around = 2, after = 3)
     */
    public abstract function getIdentifier();


    /**
     * Creates and returns the advice unique name.
     *
     * @return string The unique name of the advice
     */
    public function getUniqueName()
    {
    	// load the identifiers
        $identifier = $this->getIdentifier();
        $aspectClassName = get_class($this->getPointcut()->getAspect());
        $interceptWithMethod = $this->getPointcut()->interceptWithMethod();
		// return the concatenated unique name
        return "$identifier::$aspectClassName::$interceptWithMethod";
    }

    /**
     * String representation of the advice.
     *
     * @return string The string representation
     */
    public function __toString()
    {
        return "{$this->getOrder()}/{$this->getUniqueName()}";
    }

    /**
     * Invokes the aspect with the method to intercept.
     *
     * @param TechDivision_AOP_Interfaces_JoinPoint $joinPoint
     * 		The join point with the information to intercept
     * @return void
     */
    public function invoke(TechDivision_AOP_Interfaces_JoinPoint $joinPoint)
    {
        // load the aspect instance
        $aspect = $this->getPointcut()->getAspect();
		// load the name of the method to intercept the call with
        $methodName = $this->getPointcut()->interceptWithMethod();
		// check if the method exists
        if (!method_exists($aspect, $methodName)) {
            throw Exception(
            	'Expected method ' . $methodName .
            	' not available in class' . get_class($aspect)
            );
        }
		// invoke the method
        $aspect->$methodName($joinPoint);
		// proceed with the next Advice in the Advice Chain
		$joinPoint->getMethodInterceptor()->proceed($joinPoint);
    }
}