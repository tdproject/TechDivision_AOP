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
require_once 'TechDivision/Collections/HashMap.php';
require_once 'TechDivision/AOP/Interfaces/Pointcut.php';
require_once 'TechDivision/AOP/Interfaces/Proxy.php';
require_once 'TechDivision/AOP/Pointcuts.php';
require_once 'TechDivision/AOP/Pointcut.php';
require_once 'TechDivision/AOP/MethodInterceptor.php';
require_once 'TechDivision/AOP/Aspect/Self.php';

/**
 * This class is a wrapper for all classes an Aspect relies on.
 *
 * The class intercepts the original method call using PHP's
 * magic __call method.
 *
 * The actual version only works for non static methods.
 *
 * @package TechDivision_AOP
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
class TechDivision_AOP_Proxy_Helper
    extends TechDivision_Lang_Object {

    /**
     * The helper instance as singleton.
     * @var TechDivision_AOP_Proxy_Helper
     */
    protected static $_instance = null;

    /**
     * Returns the singleton instance.
     *
     * @return TechDivision_AOP_Proxy_Helper The helper instance
     */
    public static function get()
    {
    	// check if an instance exists
        if (self::$_instance == null) {
            self::$_instance = new TechDivision_AOP_Proxy_Helper();
        }
		// return the instance
        return self::$_instance;
    }

	/**
	 * Initializes the method interceptors for the class the
	 * Aspect relies on.
	 *
	 * @return TechDivision_Collections_HashMap
	 * 		The HashMap with the initialized method interceptors
	 */
	public function init(
	    TechDivision_AOP_Interfaces_Proxy $proxy) {
	    // intialize the HashMap for the method interceptors
	    $methodInterceptors = new TechDivision_Collections_HashMap();
	    // load all method names of the class the Aspect relies on
		$methodsToIntercept = get_class_methods(
		    get_class($proxy)
		);
		// iterate of the methods and initialize the interceptors
		for ($i = 0; $i < sizeof($methodsToIntercept); $i++) {
		    // create and append the interceptor for each method
		    $proxy->addMethodInterceptor(
	            new TechDivision_AOP_MethodInterceptor(
	                $proxy,
	                $methodsToIntercept[$i]
	            )
	        );
		}
	}
}