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

/**
 * Interface for a global cache instance.
 *
 * @package TechDivision_AOP
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
interface TechDivision_AOP_Interfaces_Cache
{

    /**
     * Test if a cache is available for the given id and (if yes)
     * return it (false else).
     *
     * @param string  $id The unique cache ID
     * @param boolean $doNotTestCacheValidity
     * 		If set to true, the cache validity won't be tested
     * @param boolean $doNotUnserialize
     * 		Do not serialize (even if automatic_serialization is true) => for internal use
     * @return mixed|false Cached datas
     */
	public function load($id, $doNotTestCacheValidity = false, $doNotUnserialize = false);

    /**
     * Test if a cache is available for the given ID.
     *
     * @param  string $id The unique cache ID
     * @return int|false
     * 		Last modified time of cache entry if it is available, false otherwise
     */
    public function test($id);

    /**
     * Save some data in a cache.
     *
     * @param mixed $data Data to put in cache (can be another type than
     * 		string if automatic_serialization is on)
     * @param string $id Cache id (if not set, the last cache id will be used)
     * @param array $tags Cache tags
     * @param int $specificLifetime If != false, set a specific lifetime
     * 		for this cache record (null => infinite lifetime)
     * @param int $priority Integer between 0 (very low priority) and 10
     * 		(maximum priority) used by some particular backends
     * @return boolean TRUE if no problem
     */
    public function save($data, $id = null, $tags = array(), $specificLifetime = false, $priority = 8);

    /**
     * Remove a cached value.
     *
     * @param string $id Cache ID to remove
     * @return boolean TRUE If value has successfully been removed
     */
    public function remove($id);
}