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
require_once 'TechDivision/AOP/Advice/Before.php';
require_once 'TechDivision/AOP/Advice/Around.php';
require_once 'TechDivision/AOP/Advice/After.php';
require_once 'TechDivision/AOP/Interfaces/Pointcut.php';

/**
 * @package TechDivision_AOP
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
class TechDivision_AOP_Advice_Helper
    extends TechDivision_Lang_Object {

	/**
	 * Initializes the available advices and their order.
	 * @var array
	 */
	public static $advices = array(
		TechDivision_AOP_Advice_Before::IDENTIFIER,
		TechDivision_AOP_Advice_Around::IDENTIFIER,
		TechDivision_AOP_Advice_After::IDENTIFIER
	);

	/**
	 * Returns TRUE if a valid advice has been passed.
	 *
	 * @param string $advice Advice to check
	 * @return boolean TRUE if a valid advice has been passed
	 */
    public static function isValidAdvice($advice)
    {
		in_array($advice, self::$advices);
    }

    /**
     * Creates a new advice instance based on the passed pointcut
     * advice information.
     * @param TechDivision_AOP_Interfaces_Pointcut $pointcut
     * 		The poincut with the advice information
     * @throws Exception Is thrown if the passed information is invalid
     * @return TechDivision_AOP_Interfaces_Advice
     * 		The requested advice instance
     */
    public static function create(
        TechDivision_AOP_Interfaces_Pointcut $pointcut) {
		// load order and type
        list($order, $type) = $pointcut->getAdvice();
		// check the type
        switch ($type) {
            case 1: // before
                return new TechDivision_AOP_Advice_Before($pointcut, $order);
                break;
            case 2: // around
                return new TechDivision_AOP_Advice_Around($pointcut, $order);
                break;
            case 3: // after
                return new TechDivision_AOP_Advice_After($pointcut, $order);
                break;
            default:
                throw new Exception("Unknown advice type $type passed");
        }
    }
}