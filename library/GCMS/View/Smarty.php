<?php
/**
 * Smarty 3 lib based on http://www.gediminasm.org/article/smarty-3-extension-for-zend-framework
 */
 
class GCMS_View_Smarty extends Zend_View_Abstract
{
    /**
     * Instance of Smarty
     * @var Smarty
     */
    protected $_smarty = null;
     
    /**
     * Template explicitly set to render in this view
     * @var string 
     */
    protected $_customTemplate = '';
     
    /**
     * Smarty config
     * @var array
     */
    private $_config = null;
 
    /**
     * Class definition and constructor
     *
     * Let's start with the class definition and the constructor part. This class is extending the Zend_View_Abstract class.
     * In the constructor the parent constructor from Zend_View_Abstract is called first. After that a Smarty object is instantiated, configured and stored in a private attribute.
     * Please note that I use a configuration object from the object store to get the configuration data for Smarty. 
     * 
     * @param array $smartyConfig
     * @param array $config
     */
    public function __construct($smartyConfig, $config = array())
    {
        $this->_config = $smartyConfig;
        parent::__construct($config);        
        $this->_loadSmarty();
    }
 
    /**
     * Return the template engine object
     *
     * @return Smarty
     */
    public function getEngine()
    {
        return $this->_smarty;
    }
     
    /**
     * Let Smarty take care of finding the correct template
     */
    protected function _script($name)
    {
        if ($this->isLfiProtectionOn() && preg_match('#\.\.[\\\/]#', $name)) {
            require_once 'Zend/View/Exception.php';
            $e = new Zend_View_Exception('Requested scripts may not include parent directory traversal ("../", "..\\" notation)');
            $e->setView($this);
            throw $e;
        }

        if ($this->_smarty->templateExists($name)) {
            return $name;
        }
        
        require_once 'Zend/View/Exception.php';
        $e = new Zend_View_Exception("script '$name' not found");
        $e->setView($this);
        throw $e;
    }

    /**
     * Implement _run() method
     *
     * The method _run() is the only method that needs to be implemented in any subclass of Zend_View_Abstract.
     * It is called automatically within the render() method. My implementation just uses the display() method from Smarty to generate and output the template.
     *
     * @param string $template
     */
    protected function _run()
    {
        $file = func_num_args() > 0 ? func_get_arg(0) : '';
        
        if ($this->_customTemplate || $file) {
            $template = $this->_customTemplate;
            if (!$template) {
                $template = $file;
            }

            $this->_smarty->display($template);
        } else {
            throw new Zend_View_Exception('Cannot render view without any template being assigned or file does not exist');
        }
    }
 
    /**
     * Simple wrapper to Smarty's append() because I use it a lot
     *
     * @param string $var
     * @param mixed $val
     * @param bool $merge
     */
    public function append($var, $val = null, $merge = false)
    {
        if (is_array($var)) {
            $this->_smarty->append($var);
        } else {
            $this->_smarty->append($var, $val, $merge);
        }
    }

    /**
     * Overwrite assign() method
     *
     * The next part is an overwrite of the assign() method from Zend_View_Abstract, which works in a similar way.
     * The big difference is that the values are assigned to the Smarty object and not to the $this->_vars variables array of Zend_View_Abstract.
     *
     * @param string|array $var
     * @param mixed $value
     * @param bool $nocache
     * @return GCMS_View_Smarty
     */
    public function assign($var, $value = null, $nocache = false)
    {
        if (is_string($var)) {
            $this->_smarty->assign($var, $value, $nocache);
        } elseif (is_array($var)) {
            $this->_smarty->assign($var);
        } else {
            throw new Zend_View_Exception('assign() expects a string or array, got '.gettype($var));
        }
        return $this;
    }
 
    /**
     * Overwrite escape() method
     *
     * The next part is an overwrite of the escape() method from Zend_View_Abstract.
     * It works both for string and array values and also uses the escape() method from the Zend_View_Abstract.
     * The advantage of this is that I don't have to care about each value of an array to get properly escaped.
     *
     * @param mixed $var
     * @return mixed
     */
    public function escape($var)
    {
        if (is_string($var)) {
            return parent::escape($var);
        } elseif (is_array($var)) {
            foreach ($var as $key => $val) {
                $var[$key] = $this->escape($val);
            }
        }
        return $var;
    }
 
    /**
     * Print the output
     *
     * The next method output() is a wrapper on the render() method from Zend_View_Abstract.
     * It just sets some headers before printing the output.
     *
     * @param <type> $name
     */
    public function output($name)
    {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Cache-Control: no-cache");
        header("Pragma: no-cache");
        header("Cache-Control: post-check=0, pre-check=0", false);
 
        print parent::render($name);
    }
 
    /**
     * Use Smarty caching
     *
     * The last two methods were created to simply integrate the Smarty caching mechanism in the View class.
     * With the first one you can check for cached template and with the second one you can set the caching on or of.
     *
     * @param string $template
     * @return bool
     */
    public function isCached($template)
    {
        return $this->_smarty->is_cached($template);
    }
 
    /**
     * Enable/disable caching
     *
     * @param bool $caching
     * @return GCMS_View_Smarty
     */
    public function setCaching($caching)
    {
        $this->_smarty->caching = $caching;
        return $this;
    }
 
    /**
     * Template getter (return file path)
     * @return string
     */
    public function getTemplate()
    {
        return $this->_customTemplate;
    }
 
    /**
     * Template filename setter
     * @param string
     * @return GCMS_View_Smarty
     */
    public function setTemplate($tpl)
    {
        $this->_customTemplate = $tpl;
        return $this;
    }
 
    /**
     * Magic setter for Zend_View compatibility. Performs assign()
     *
     * @param string $key
     * @param mixed $val
     */
    public function __set($key, $val)
    {
        $this->assign($key, $val);
    }
 
 
    /**
     * Magic getter for Zend_View compatibility. Retrieves template var
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->_smarty->getTemplateVars($key);
    }
     
    /**
     * Magic getter for Zend_View compatibility. Removes template var
     * 
     * @see View/Zend_View_Abstract::__unset()
     * @param string $key
     */
    public function __unset($key)
    {
        $this->_smarty->clearAssign($key);
    }
     
    /**
     * Allows testing with empty() and isset() to work
     * Zend_View compatibility. Checks template var for existance
     *
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        return (null !== $this->_smarty->getTemplateVars($key));
    }
 
    /**
     * Zend_View compatibility. Retrieves all template vars
     * 
     * @see Zend_View_Abstract::getVars()
     * @return array
     */
    public function getVars()
    {
        return $this->_smarty->getTemplateVars();
    }
     
    /**
     * Updates Smarty's template_dir field with new value
     *
     * @param string $dir
     * @return GCMS_View_Smarty
     */
    public function setTemplateDir($dir)
    {
        $this->_smarty->setTemplateDir($dir);
        return $this;
    }
     
    /**
     * Adds another Smarty template_dir to scan for templates
     *
     * @param string $dir
     * @return GCMS_View_Smarty
     */
    public function addTemplateDir($dir)
    {
        $this->_smarty->addTemplateDir($dir);
        return $this;
    }
     
    /**
     * Adds another Smarty plugin directory to scan for plugins
     *
     * @param string $dir
     * @return GCMS_View_Smarty
     */
    public function addPluginDir($dir)
    {
        $this->_smarty->addPluginsDir($dir);
        return $this;
    }
     
    /**
     * Zend_View compatibility. Removes all template vars
     * 
     * @see View/Zend_View_Abstract::clearVars()
     * @return GCMS_View_Smarty
     */
    public function clearVars()
    {
        $this->_smarty->clearAllAssign();
        $this->assign('view', $this);
        return $this;
    }
     
    /**
     * Zend_View compatibility. Add the templates dir
     * 
     * @see View/Zend_View_Abstract::addBasePath()
     * @return GCMS_View_Smarty
     */
    public function addBasePath($path, $classPrefix = 'Zend_View')
    {
        parent::addBasePath($path, $classPrefix);
        // For some dumb reason Zend_View::addScript() is in LIFO order.  We need to fix it.
        $this->addScriptPath($path . '/templates');
        //$backwards = array_reverse($this->getScriptPaths());
        //$this->setScriptPath(null);
        //foreach ($backwards as $v) {
        //    $this->addScriptPath($v);
        //}
        $this->addTemplateDir(array($path . '/templates', $path . '/templates/_static'));
        return $this;
    }
     
    /**
     * Zend_View compatibility. Set the templates dir instead of scripts
     * 
     * @see View/Zend_View_Abstract::setBasePath()
     * @return GCMS_View_Smarty
     */
    public function setBasePath($path, $classPrefix = 'Zend_View')
    {
        parent::setBasePath($path, $classPrefix);
        $this->setScriptPath($path . '/templates');
        $this->setTemplateDir(array($path . '/templates', $path . '/templates/_static'));
        return $this;
    }
     
    /**
     * Magic clone method, on clone create diferent smarty object
     */
    public function __clone() {
        $this->_loadSmarty();
    }
     
    /**
     * Initializes the smarty and populates config params
     * 
     * @throws Zend_View_Exception
     * @return void
     */
    private function _loadSmarty()
    {
        if (!class_exists('Smarty', true)) {
            require_once 'Smarty.class.php';
        }
         
        $this->_smarty = new Smarty();
 
        if ($this->_config === null) {
            throw new Zend_View_Exception("Could not locate Smarty config - node 'smarty' not found");
        }
 
        $this->_smarty->caching = $this->_config['caching'];
        $this->_smarty->cache_lifetime = $this->_config['cache_lifetime'];
        $this->_smarty->template_dir = $this->_config['template_dir'];
        $this->_smarty->compile_dir = $this->_config['compile_dir'];
        $this->_smarty->config_dir = $this->_config['config_dir'];
        $this->_smarty->cache_dir = $this->_config['cache_dir'];
        $this->_smarty->left_delimiter = $this->_config['left_delimiter'];
        $this->_smarty->right_delimiter = $this->_config['right_delimiter'];
        $this->assign('view', $this);
    }
}
?>
