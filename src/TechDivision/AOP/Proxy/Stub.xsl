<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    						  xmlns:str="http://xsltsl.org/string"
     						  xmlns:php="http://php.net/xsl">

	<xsl:output encoding="UTF-8" method="text"/>

	<xsl:template match="aspectable">
		<xsl:text disable-output-escaping="yes">&lt;?php</xsl:text>

        require_once 'TechDivision/Collections/HashMap.php';
        require_once 'TechDivision/AOP/Proxy/Helper.php';
        require_once 'TechDivision/AOP/Interfaces/Proxy.php';

        class <xsl:value-of select="@proxyClassName"/>
            extends <xsl:value-of select="@className"/> 
            implements TechDivision_AOP_Interfaces_Proxy {

		    /**
		     * The Pointcuts.
		     * @var TechDivision_AOP_Pointcuts
		     */
		    protected $_pointcuts = null;
		
		    /**
		     * HashMap with the method interceptors.
		     * @var TechDivision_Collections_HashMap
		     */
		    protected $_methodInterceptors = null;
		    
		    /**
		     * The Context the Proxy uses.
		     * @var TechDivision_Collections_HashMap
		     */
		    protected $_proxyContext = null;
		    
		    /**
		     * The cache instance to use
		     * @var TechDivision_AOP_Interfaces_Cache
		     */
		    protected $_proxyCache = null;
		    
		    /**
		     * Array with the available method signatures.
		     * @var array
		     */
            protected $_methodSignatures = array(
            <xsl:if test="methods"><xsl:for-each select="methods/method">
                '<xsl:value-of select="@name"/>' => '<xsl:value-of select="@methodSignature"/>'<xsl:if test="position()!=last()">, </xsl:if>
            </xsl:for-each></xsl:if>);
            
            /**
             * Returns the method signature for the method
             * with the passed name.
             *
             * @param string $methodName Name of the method to return the signature for
             * @return string The method signature
             */
            public function getMethodSignature($methodName)
            {
                if (array_key_exists($methodName, $this-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>_methodSignatures)) {
                    return $this-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>_methodSignatures[$methodName];
                }
            }
            
            /**
             * Sets the cache instance to use.
             *
             * @var TechDivision_AOP_Interfaces_Cache The cache instance to use
             * @return TechDivision_AOP_Interfaces_Proxy The proxy instance
             */
            public function setProxyCache(
                TechDivision_AOP_Interfaces_Cache $proxyCache)
            {
                $this-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>_proxyCache = $proxyCache;
                return $this;
            }
            
            /**
             * Returns the cache instance to use.
             * 
             * @return TechDivision_AOP_Interfaces_Cache The cache instance
             */
            public function getProxyCache()
            {
                if ($this-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>_proxyCache == null) {
                    $this-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>_proxyCache = new TechDivision_AOP_Proxy_Cache();
                }
                return $this-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>_proxyCache;
            }

            /**
             * Returns the value with the key of the argument 
             * in the passed array.
             *
             * @param string $key The argument name to return the value for
             * @param array Array with the argument values
             * @return The value if available
             */
            public static function ___getArgumentValue($key, $arguments)
            {
                if (array_key_exists($key, $arguments)) {
                    return $arguments[$key];
                }
            }
		
		    /**
		     * Initialize the Container with the oject
		     * to rely the Aspect on.
		     *
		     * @return void
		     */
		    public final function initProxy()
		    {
		        $this-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>_pointcuts = new TechDivision_AOP_Pointcuts();
		        $this-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>_methodInterceptors = new TechDivision_Collections_HashMap();
                $this-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>_proxyContext = new TechDivision_Collections_HashMap();
		        TechDivision_AOP_Proxy_Helper::get()-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>init($this);
		        return $this;
		    }
		
		    /**
		     * Returns the Pointcuts.
		     *
		     * @return TechDivision_AOP_Pointcuts
		     *      The Pointcuts
		     */
		    public function getPointcuts()
		    {
		        return $this-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>_pointcuts;
		    }
        
            /**
             * Returns the MethodInterceptors.
             *
             * @return TechDivision_Collections_HashMap
             *      The MethodInterceptors
             */
            public function getMethodInterceptors()
            {
                return $this-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>_methodInterceptors;
            }
		
		    /**
		     * Adds a the passed Pointcut.
		     *
		     * @param TechDivision_AOP_Interfaces_Pointcut $pointcut
		     *      The Pointcut to add
		     * @return TechDivision_AOP_Interfaces_Proxy
		     *      The instance itself
		     */
		    public function addPointcut(
		        TechDivision_AOP_Interfaces_Pointcut $pointcut) {
		        $this-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>getPointcuts()-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>add($pointcut);
		        return $this;
		    }
        
            /**
             * Adds a the passed MethodInterceptor.
             *
             * @param TechDivision_AOP_Interfaces_MethodInterceptor $methodInterceptor
             *      The MethodInterceptor to add
             * @return TechDivision_AOP_Interfaces_Proxy
             *      The instance itself
             */
            public function addMethodInterceptor(
                TechDivision_AOP_Interfaces_MethodInterceptor $methodInterceptor) {
                $this-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>getMethodInterceptors()-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>add($methodInterceptor->getMethod(), $methodInterceptor);
                return $this;
            }

		    /**
		     * The object the Aspect relies on
		     *
		     * @return TechDivision_Lang_Object
		     *      The object the Aspect relies on
		     */
		    public function getAspectable()
		    {
		        return $this;
		    }

            /**
             * Returns the Context the Proxy is in.
             *
             * @return TechDivision_Collections_HashMap
             */
            public function getProxyContext()
            {
                return $this-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>_proxyContext;
            }
            
            <xsl:if test="methods">
		    <xsl:for-each select="methods/method">		    
		    public function <xsl:if test="@returnsReference=1">&amp;</xsl:if><xsl:value-of select="@name"/>(<xsl:call-template name="arguments-signature"/>)
            {
                // create a md5 hash of the method signature
                $signature = md5($this-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>getMethodSignature('<xsl:value-of select="@name"/>'));
                // check if the method as already been intercepted without matching pointcut
                if ($this-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>getProxyCache()-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>test($signature) &amp;&amp;
                    $this-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>getProxyCache()-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>load($signature) === false) {
                    // if yes, call the parent method immediately
                    return parent::<xsl:value-of select="@name"/>(<xsl:call-template name="arguments-pass-through"/>);
                }
                // check if a MethodInterceptor exists
                if (($methodInterceptors = $this-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>getMethodInterceptors()) != null) {
                    if ($methodInterceptors-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>exists('<xsl:value-of select="@name"/>')) {                
                       if ($methodInterceptors-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>get('<xsl:value-of select="@name"/>')-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>match()) {
                            // mark the method to be intercepted
                            $this-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>getProxyCache()-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>save(true, $signature);
                            // if yes, invoke it and return the result
                            return $methodInterceptors-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>get('<xsl:value-of select="@name"/>')-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>intercept(func_get_args());
                       }
                    }
                }
                // mark the method to prevent interception
                $this-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>getProxyCache()-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>save(false, $signature);
                // call the parent method finally 
                return parent::<xsl:value-of select="@name"/>(<xsl:call-template name="arguments-pass-through"/>);
            }           
            
            public function ___<xsl:value-of select="@name"/>(array $arguments)
            {   
                $counter = 0;
                return parent::<xsl:value-of select="@name"/>(<xsl:call-template name="arguments-pass-through-as-array"/>);
            }
            </xsl:for-each></xsl:if>}

	</xsl:template>

    <xsl:template name="interfaces">
        <xsl:if test="interfaces">
            implements <xsl:for-each select="interfaces/interface">
                <xsl:value-of select="@name"/><xsl:if test="position()!=last()">, </xsl:if>
            </xsl:for-each>
        </xsl:if>
    </xsl:template>

    <xsl:template name="method-signature">
        <xsl:if test="arguments">
            <xsl:for-each select="arguments/argument">
                <xsl:if test="@type"><xsl:value-of select="@type"/><xsl:text> </xsl:text></xsl:if>$<xsl:value-of select="@name"/><xsl:if test="@default"> = <xsl:value-of select="@default"/></xsl:if> <xsl:if test="position()!=last()">, </xsl:if>
            </xsl:for-each>
        </xsl:if>
    </xsl:template>

    <xsl:template name="arguments-signature">
        <xsl:if test="arguments">
            <xsl:for-each select="arguments/argument">
                <xsl:if test="@type"><xsl:value-of select="@type"/><xsl:text> </xsl:text></xsl:if><xsl:if test="@passedByRef">&amp;</xsl:if>$<xsl:value-of select="@name"/><xsl:if test="@default"> = <xsl:value-of select="@default"/></xsl:if> <xsl:if test="position()!=last()">, </xsl:if>
            </xsl:for-each>
        </xsl:if>
    </xsl:template>

    <xsl:template name="arguments-pass-through">
        <xsl:if test="arguments"><xsl:for-each select="arguments/argument">$<xsl:value-of select="@name"/><xsl:if test="position()!=last()">, </xsl:if></xsl:for-each></xsl:if>
    </xsl:template>

    <xsl:template name="arguments-pass-through-as-array">
        <xsl:if test="arguments"><xsl:for-each select="arguments/argument">self::___getArgumentValue($counter++, $arguments)<xsl:if test="position()!=last()">, </xsl:if></xsl:for-each></xsl:if>
    </xsl:template>
        
</xsl:stylesheet>