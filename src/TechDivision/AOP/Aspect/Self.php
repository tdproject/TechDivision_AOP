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

require_once 'TechDivision/AOP/Aspect.php';

/**
 * @package TechDivision_AOP
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
class TechDivision_AOP_Aspect_Self extends TechDivision_AOP_Aspect
{

	/**
	 * Invokes the original method of the class the proxy is for.
	 *
	 * @param TechDivision_AOP_Interfaces_JoinPoint $joinPoint
	 * 		The join point with the class instance
	 * @return mixed The method's result
	 */
    public function __invoke(TechDivision_AOP_Interfaces_JoinPoint $joinPoint)
    {
        return $joinPoint->proceed();
    }
}