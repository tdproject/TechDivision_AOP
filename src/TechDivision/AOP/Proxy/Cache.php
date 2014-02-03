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
require_once 'TechDivision/AOP/Interfaces/Cache.php';

/**
 * This is basic cache implementation for testing usage only.
 *
 * It is recommended to use real cache implementation like
 * Zend_Cache extended to also implement the interface.
 *
 * @package TechDivision_AOP
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
class TechDivision_AOP_Proxy_Cache
    extends TechDivision_Lang_Object
    implements TechDivision_AOP_Interfaces_Cache {

    /**
     * Array for temporary caching.
     * @var array
     */
    protected $_cache = array();

    /**
     * (non-PHPdoc)
     * @see TechDivision_AOP_Interfaces_Cache::load()
     */
	public function load($id, $doNotTestCacheValidity = false, $doNotUnserialize = false)
	{
		// check if value is in cache
		if ($this->test($id)) {
        	return $this->_cache[$id];
		}
		// return null instead
		return null;
	}

    /**
     * (non-PHPdoc)
     * @see TechDivision_AOP_Interfaces_Cache::test()
     */
    public function test($id)
    {
        return array_key_exists($id, $this->_cache);
    }

    /**
     * (non-PHPdoc)
     * @see TechDivision_AOP_Interfaces_Cache::save()
     */
    public function save($data, $id = null, $tags = array(), $specificLifetime = false, $priority = 8)
    {
        $this->_cache[$id] = $data;
    }

    /**
     * (non-PHPdoc)
     * @see TechDivision_AOP_Interfaces_Cache::remove()
     */
    public function remove($id)
    {
        unset($this->_cache[$id]);
    }
}