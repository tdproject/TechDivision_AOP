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

require_once 'TechDivision/AOP/Advice/Abstract.php';

/**
 * @package TechDivision_AOP
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
class TechDivision_AOP_Advice_Around
    extends TechDivision_AOP_Advice_Abstract {

	/**
	 * Unique identifier of the advice.
	 * @var integer
	 */
    const IDENTIFIER = 2;

    /**
     * (non-PHPdoc)
     * @see TechDivision_AOP_Advice_Abstract::getIdentifier()
     */
    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }

    /**
     * (non-PHPdoc)
     * @see TechDivision_AOP_Advice_Abstract::invoke()
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
		// invoke the method and set the result
        $joinPoint->setResult($aspect->$methodName($joinPoint));
		// proceed with the next advice
		return $joinPoint->getMethodInterceptor()->proceed($joinPoint);
    }
}