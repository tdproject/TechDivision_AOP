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
require_once 'TechDivision/AOP/Proxy/CreateException.php';

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
class TechDivision_AOP_Proxy_Generator extends DOMDocument
{

    /**
     * The Proxy's interfaces to render.
     * @var array
     */
    protected $_interfaces = array();

    /**
     * The reflection class to render the Proxy for.
     * @var ReflectionClass
     */
    protected $_reflectionClass = null;

    /**
     * Factory method to create a new instance of the proxy generator.
     *
     * @return TechDivision_AOP_Proxy_Generator The instance
     */
    public static function get()
    {
        return new TechDivision_AOP_Proxy_Generator();
    }

    /**
     * Sets the reflection class of the class to create the proxy for.
     *
     * @param ReflectionClass $reflectionClass
     * 		The reflection class to create the proxy for
     */
    public function setReflectionClass(ReflectionClass $reflectionClass)
    {
        $this->_reflectionClass = $reflectionClass;
    }

    /**
     * Returns the reflection class of the class to create the proxy for.
     *
     * @return ReflectionClass The reflection class to create the proxy for
     */
    public function getReflectionClass()
    {
        return $this->_reflectionClass;
    }

    /**
     * Returns the class name to create the proxy for.
     *
     * @return string The name of the class to create the proxy for
     */
    public function getClassName()
    {
        return $this->getReflectionClass()->getName();
    }

    /**
     * Returns the class name of the proxy.
     *
     * @return string The proxy class name
     */
    public function getProxyClass()
    {
        return $this->getClassName() . '_Proxy' . filectime($this->getFileName());
    }

    /**
     * Returns the filename of the class to create the proxy for.
     *
     * @return string Filename of the class to create the proxy for
     */
    public function getFileName()
    {
        return $this->getReflectionClass()->getFileName();
    }

    /**
     * Returns the generated proxy filename.
     *
     * @return string The proxy filename
     */
    public function getProxyFileName()
    {
        return str_replace('_', '/', $this->getProxyClass()) . '.php';
    }

    /**
     * Creates a new proxy for the passed class and returns the instance.
     *
     * @param string $className Name of the class to proxy
     * @param array $arguments Arguments to pass to the constructor
     * @return object The proxy instance
     */
    public function newProxy($className, array $arguments = array())
    {
    	// creates a new reflection class of the class name to proxy for
        $this->setReflectionClass(new ReflectionClass($className));
		// load the proxy class name
        $reflectionClass = new ReflectionClass($this->load());
		// create a new instance of the proxy and return it instead of the class
        return $reflectionClass->newInstanceArgs($arguments)->initProxy();
    }

    /**
     * (non-PHPdoc)
     * @see DOMDocument::load()
     */
    public function load($filename = '', $options = 0)
    {
    	// create the proxy source
        $source = $this->create();
		// load the proxy filename
        $proxyFileName = $this->getProxyFileName();
		// set the directory to store the sources in
        $dir = './' . dirname($proxyFileName);
		// if not a directory, create it
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
		// save the proxy sources as file
        file_put_contents($proxyFileName, $source);
		// include the proxy
        require_once $proxyFileName;
		// create name of the proxy class
        return $this->getProxyClass();
    }

    /**
     * Creates and returns the sources for the Proxy class by rendering the
     * XML structure and transforming it with a XSLT transformation.
     *
     * @return string The source for the Proxy class
     * @throws TechDivision_AOP_Proxy_CreateException
     * 		Is thrown if an error occurs, e. g. when creating the XML structure
     */
    public function create()
    {
        // create the Proxy source and return it
        try {
            // create the XML root node
            $aspectable = $this->createElement('aspectable');
            $aspectable->setAttribute('className', $this->getClassName());
            $aspectable->setAttribute('proxyClassName', $this->getProxyClass());
            // append the interfaces and the methods
            $this->appendInterfaces($aspectable);
            $this->appendMethods($aspectable);
            $this->appendChild($aspectable);
            // initialize the XSLTProcessor instance
            $processor = new XSLTProcessor();
            // load the XSL stylesheet
            $xsl = new DOMDocument();
            $xsl->loadXML(
                file_get_contents('TechDivision/AOP/Proxy/Stub.xsl', true)
            );
            // import the sytlesheet
            $processor->importStyleSheet($xsl);
            // transform the XML into the soure
            return $processor->transformToXML($this);
        } catch(Exception $e) {
            throw new TechDivision_AOP_Proxy_CreateException(
            	'Error when generate Proxy for ' . $this->getClassName(), 0, $e
            );
        }
    }

    /**
     * Appends a list with all interfaces a class implements to the passed
     * DOM node.
     *
     * @param DOMElement $aspectable The root node to append the interfaces to
     * @return TechDivision_AOP_Proxy_Generator
     * 		The instance itself
     */
    public function appendInterfaces(DOMElement $aspectable)
    {
		// load a classes interfaces (can be double, and has to be cleared)
        $this->_interfaces = array_flip(
        	$this->getReflectionClass()->getInterfaceNames()
        );
		// clear the interface names
        foreach ($this->getReflectionClass()->getInterfaces() as $interface) {
            $this->_appendInterface($interface);
        }
		// load the cleared interface names
        $this->_interfaces = array_values(array_flip($this->_interfaces));
		// check if interfaces has been found
        if ($interfacesFound = sizeof($this->_interfaces)) {
            $interfaces = $this->createElement('interfaces');
            // create a new DOMElement
            for ($i = 0; $i < $interfacesFound; $i++) {
                $interface = $this->createElement('interface');
                $interface->setAttribute('name', $this->_interfaces[$i]);
                $interfaces->appendChild($interface);
            }
			// append the DOMElement
            $aspectable->appendChild($interfaces);
        }
		// return the instance
        return $this;
    }

    /**
     * Reduces the interfaces the passed class implements and sets the
     * cleared result in the array with the unique interface names.
     *
     * @param ReflectionClass $reflectionClass Reflection class itself
     */
    protected function _appendInterface(ReflectionClass $reflectionClass)
    {
		// iterate over all interfaces the passed class implements
        foreach ($reflectionClass->getInterfaces() as $interface) {
			// check if the interface has already been added
            if (array_key_exists($interface->getName(), $this->_interfaces)) {
            	// if yes, unset it
                unset($this->_interfaces[$interface->getName()]);
            }
        }
    }

    /**
     * Appends the methods arguments to the XML Document.
     *
     * @param DOMElement $aspectable The root node to append to
     * @return TechDivision_AOP_Proxy_Generator
     * 		The instance itself
     */
    public function appendMethods(DOMElement $aspectable)
    {
		// load the classes reflection method
        $reflectionMethods = $this->getReflectionClass()->getMethods();
		// check if the class has methods
        if ($methodsFound = sizeof($reflectionMethods)) {
            $methods = $this->createElement('methods');
            foreach ($reflectionMethods as $reflectionMethod) {
                if ($reflectionMethod->isPublic() &&
                	$reflectionMethod->isConstructor() === false &&
                	$reflectionMethod->isDestructor() === false &&
                	$reflectionMethod->isAbstract() === false &&
                	$reflectionMethod->isStatic() === false &&
                	$reflectionMethod->isFinal() === false) {
                	// append an element for the method
                	$method = $this->createElement('method');
                	$method->setAttribute('name', $reflectionMethod->getName());
                	$method->setAttribute('methodSignature', $this->getMethodSignature($reflectionMethod));
                	$method->setAttribute('returnsReference', $reflectionMethod->returnsReference());
                	$method->appendChild($this->appendArguments($reflectionMethod));
                	$methods->appendChild($method);
                }
            }
			// append the element with the methods to the passed DOM node
            $aspectable->appendChild($methods);
        }
		// return the instance itself
        return $this;
    }

    /**
     * Returns a string representation of the passed methods signature in
     * form of
     *
     * 		modifier className->methodName(type $arg = $default)
	 *
     * @param ReflectionMethod $reflectionMethod
     * 		The reflection method to create the string representation for
     * @return string String representation of the passed method's signature
     */
    public function getMethodSignature(ReflectionMethod $reflectionMethod)
    {
		// initialize the class, method and modifier names
    	$className = $this->getReflectionClass()->getName();
    	$methodName = $reflectionMethod->getName();
    	$modifierNames = $this->getModifierNames($reflectionMethod);
		// initialize the array for the arguments
    	$arguments = array();
		// check if the method has parameters
    	if ($reflectionMethod->getNumberOfParameters() > 0) {
			// iterate of the method's parameters
	    	foreach ($reflectionMethod->getParameters() as $parameter) {
				// initialize the string for the argument
		    	$argument = '';
				// check if the argument has a type
		    	if ($class = $parameter->getClass()) {
		    		$argument .= "{$class->getName()} ";
		    	} elseif ($isArray = $parameter->isArray()) {
		    		$argument .= "array ";
		    	}
				// add the argument name
		    	$argument .= "\${$parameter->getName()} ";
		    	/*
		    	if ($parameter->isDefaultValueAvailable()) {
			    	$defaultValue = $parameter->getDefaultValue();
		    		$argument .= "= {$this->_appendArgument($defaultValue)}";
		    	}
		    	*/
				// add the argument
		    	$arguments[] = trim($argument);

	    	}
    	}
		// implode and escape the string with the arguments
    	$args = addslashes(implode(', ', $arguments));
		// concatenate the method signature and return it
    	return "$modifierNames $className->$methodName($args)";
    }

    /**
     * Returns an string representation of the modifier names for the passed
     * reflection method.
     *
     * @param ReflectionMethod $reflectionMethod
     * 		The reflection method to return the modifiers for
     * @return string The string representation of the methods modifiers
     */
    public function getModifierNames(ReflectionMethod $reflectionMethod)
    {
    	// load the modifier names
    	$modifierNames = Reflection::getModifierNames(
    		$reflectionMethod->getModifiers()
    	);
		// implode the array and return the string
    	return implode(' ', $modifierNames);
    }

    /**
     * Appends the passed reflection method's arguments as DOM nodes
     * to the DOMDocument itself.
     *
     * @param ReflectionMethod $reflectionMethod
     * 		The reflection method to append the arguments
     * @return DOMElement The arguments
     */
    public function appendArguments(ReflectionMethod $reflectionMethod)
    {
		// create a new DOMElement for the arguments
        $arguments = $this->createElement('arguments');
		// check if the method has arguments
        if ($reflectionMethod->getNumberOfParameters() > 0) {
			// if yes, iterate over all arguments
            foreach ($reflectionMethod->getParameters() as $parameter) {
				// create a DOMElement for the argument
                $argument = $this->createElement('argument');
				// append the type if available
                if ($class = $parameter->getClass()) {
                    $argument->setAttribute('type', $class->getName());
                } elseif ($isArray = $parameter->isArray()) {
                    $argument->setAttribute('type', 'array');
                }
				// append the parameter name itself
                $argument->setAttribute('name', $parameter->getName());
	            // check if arguments are passed by reference
                if ($parameter->isPassedByReference()) {
					// append attribute with passed by ref flag
                    $argument->setAttribute('passedByRef', true);
                }
				// check if a default value is available
                if ($parameter->isDefaultValueAvailable()) {
					// load the default valued
                    $defaultValue = $parameter->getDefaultValue();
					// append it to the argument
                    $argument->setAttribute(
                    	'default',
                        $this->_appendArgument($defaultValue)
                    );
                }
				// append the argument itself
                $arguments->appendChild($argument);
            }
        }
		// return the DOMElement with the method's arguments
        return $arguments;
    }

    /**
     * Prepares the passed argument default value to be
     * appended to the DOMElement.
     *
     * @param mixed $value The value to prepare
     * @return string The prepared argument value
     */
    protected function _appendArgument($value)
    {
		// initialize the default value
        $default = null;
		// check for an array
        if (is_array($value)) {
			// prepare array values
            $values = array();
			// call the method recursive to prepare the array's values
            foreach ($value as $key => $val) {
                $values[] = $this->_appendArgument($key) . '=>' . $this->_appendArgument($val);
            }
			// append all values
            $default = 'array(' . implode(', ', $values) . ')';
        }
        // check for a numeric value
        elseif(is_numeric($value)) {
            $default = $value;
        }
        // check for a string value
        elseif(is_string($value)) {
            $default = "'" . addslashes($value) . "'";
        }
        // check for a boolean value
        elseif(is_bool($value)) {
        	if ($value) {
        		$default = 'true';
        	}
        	else {
        		$default = 'false';
        	}
        }
        // else set it null
        else {
            $default = "null";
        }
		// return the prepared default value
        return $default;
    }
}