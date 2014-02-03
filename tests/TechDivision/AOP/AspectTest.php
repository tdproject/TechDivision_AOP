<?php

require_once 'TechDivision/AOP/Pointcut.php';
require_once 'TechDivision/AOP/TestAspectable.php';
require_once 'TechDivision/AOP/TestAspect.php';
require_once 'TechDivision/AOP/TestAspect2.php';
require_once 'TechDivision/AOP/TestAspect3.php';
require_once 'TechDivision/AOP/TestAspect4.php';
require_once 'TechDivision/AOP/LogTarget.php';
require_once 'TechDivision/AOP/Proxy/Cache.php';
require_once 'TechDivision/AOP/Proxy/Generator.php';

/**
 * This class provides the tests for the
 * Aspect manager.
 *
 * @package TechDivision_AOP
 * @author wagnert <t.wagner@techdivision.com>
 */
class TechDivision_AOP_AspectTest extends PHPUnit_Framework_TestCase {

    public function testWithLogTargetAfter()
    {

    	$logTarget = new TechDivision_AOP_LogTarget();

    	$proxy = TechDivision_AOP_Proxy_Generator::get()
    	    ->newProxy('TechDivision_AOP_TestAspectable', array($name = 'foo'));

    	$proxy
    	    ->addPointcut(
    	        TechDivision_AOP_Pointcut::create()
        	        ->aspect(new TechDivision_AOP_TestAspect2($logTarget))
        	    	->intercept('.* .*->app.*(.*)')
        	    	->after()
        	    	->withMethod('log')
        	    );

    	$toTest = $proxy->append($arg1 = 'bar');

    	$this->assertEquals('foobar', $logTarget->write());
    	$this->assertEquals('foobar', $toTest);
    }

    public function testWithLogTargetBefore()
    {

    	$logTarget = new TechDivision_AOP_LogTarget();

    	$proxy = TechDivision_AOP_Proxy_Generator::get()
    	    ->newProxy('TechDivision_AOP_TestAspectable', array($name = 'foo'));

    	$proxy
    	    ->addPointcut(
    	        TechDivision_AOP_Pointcut::create()
        	        ->aspect(new TechDivision_AOP_TestAspect2($logTarget))
        	        ->intercept('.* .*->app.*(.*)')
        	        ->before()
        	        ->withMethod('log')
        	    );

    	$toTest = $proxy->append($arg1 = 'bar');

    	$this->assertEquals($name, $logTarget->write());
    	$this->assertEquals('foobar', $toTest);
    }

    public function testAdviceAround()
    {

    	$logTarget = new TechDivision_AOP_LogTarget();

    	$proxy = TechDivision_AOP_Proxy_Generator::get()
    	    ->newProxy('TechDivision_AOP_TestAspectable', array($name = 'foo'));

    	$proxy
    	    ->addPointcut(
    	        TechDivision_AOP_Pointcut::create()
        	        ->aspect(new TechDivision_AOP_TestAspect3($logTarget))
        	    	->intercept('.* .*->app.*(.*)')
        	    	->around()
        	    	->withMethod('log')
        	    );

    	$toTest = $proxy->append($arg1 = 'bar');

    	$this->assertEquals('foobar', $logTarget->write());
    	$this->assertEquals('foobar', $toTest);
    }

    public function testAdviceAroundStopExcection()
    {

    	$logTarget = new TechDivision_AOP_LogTarget();

    	$proxy = TechDivision_AOP_Proxy_Generator::get()
    	    ->newProxy('TechDivision_AOP_TestAspectable', array($name = 'foo'));

    	$proxy
    	    ->addPointcut(
    	        TechDivision_AOP_Pointcut::create()
        	        ->aspect(new TechDivision_AOP_TestAspect4($logTarget))
        	    	->intercept('.* .*->app.*(.*)')
        	    	->around()
        	    	->withMethod('log')
        	    );

    	$toTest = $proxy->append($arg1 = 'bar');

    	$this->assertEquals('foo', $logTarget->write());
    	$this->assertEquals('barfoo', $toTest);
    }

    public function testWithLogTargetBeforeAndAfter()
    {

    	$logTarget1 = new TechDivision_AOP_LogTarget();
    	$logTarget2 = new TechDivision_AOP_LogTarget();

    	$proxy = TechDivision_AOP_Proxy_Generator::get()
    	    ->newProxy('TechDivision_AOP_TestAspectable', array($name = 'foo'));

    	$proxy
    	    ->addPointcut(
    	        TechDivision_AOP_Pointcut::create()
        	        ->aspect(new TechDivision_AOP_TestAspect2($logTarget1))
        	    	->intercept('.* .*->app.*(.*)')
        	    	->before()
        	    	->withMethod('log')
    	    );

    	$proxy
    	    ->addPointcut(
    	        TechDivision_AOP_Pointcut::create()
        	        ->aspect(new TechDivision_AOP_TestAspect2($logTarget2))
        	    	->intercept('.* .*->app.*(.*)')
        	    	->after()
        	    	->withMethod('log')
        	);

    	$toTest = $proxy->append($arg1 = 'bar');

    	$this->assertEquals('foo', $logTarget1->write());
    	$this->assertEquals('foobar', $logTarget2->write());
    	$this->assertEquals('foobar', $toTest);
    }

    public function testCallWithPointcutAndException()
    {

    	$this->setExpectedException('Exception');

    	$proxy = TechDivision_AOP_Proxy_Generator::get()
    	    ->newProxy('TechDivision_AOP_TestAspectable', array($name = 'foo'));

    	$proxy
    	    ->addPointcut('.* .*->app.*(.*)', 'before', 'failWithException')
    	        ->setAspect(new TechDivision_AOP_TestAspect2($logTarget1));

    	$toTest = $proxy->append($arg1 = 'bar');
    }

    public function testCallWithoutPointcut()
    {

    	$proxy = TechDivision_AOP_Proxy_Generator::get()
    	    ->newProxy('TechDivision_AOP_TestAspectable', array($name = 'foo'));

    	$toTest = $proxy->append($toAppend = 'bar');

    	$this->assertEquals($name . $toAppend, $proxy->getName());
    }
}