<?php

namespace LeMike\PhpSpec\WpExtension\Autoloader;

/**
 * Classes source autoload
 */
class WpLoader
{
    const SCOPE_FILE_PREFIX = '__';

    static protected $_instance;
    static protected $_scope = 'default';

    protected $_isIncludePathDefined= null;
    protected $_collectClasses      = false;
    protected $_collectPath         = null;
    protected $_arrLoadedClasses    = array();
    protected $_srcPath = '';
    protected $_codePool = '';

    /**
     * Class constructor
     */
    public function __construct($srcPath)
    {
        $this->_srcPath = $srcPath;
        $this->_isIncludePathDefined = defined('COMPILER_INCLUDE_PATH');
        if (defined('COMPILER_COLLECT_PATH')) {
            $this->_collectClasses  = true;
            $this->_collectPath     = COMPILER_COLLECT_PATH;
        }
        set_include_path(get_include_path() . PATH_SEPARATOR . $this->_srcPath);
        self::registerScope(self::$_scope);
    }

    /**
     * Singleton pattern implementation
     *
     * @return WpLoader
     */
    static public function instance($srcPath, $codePool)
    {
        if (!self::$_instance) {
            self::$_instance = new WpLoader($srcPath, $codePool);
        }
        return self::$_instance;
    }

    /**
     * Register SPL autoload function
     */
    static public function register($srcPath, $codePool)
    {
        spl_autoload_register(array(self::instance($srcPath, $codePool), 'autoload'));
    }

	public static function classToFile( $className ) {

		$classSanitize = strtr(
			$className,
			array(
				'_' => '-'
			)
		);

		$classSanitize = strtolower( $classSanitize );

		$parts = explode( '\\', $classSanitize );

		$file = array_pop( $parts );

		$fileDir = strtolower(
			implode( DIRECTORY_SEPARATOR, $parts )
			. DIRECTORY_SEPARATOR . 'class-' . $file
		);

		return $fileDir;
	}

    /**
     * Load class source code
     *
     * @param string $class
     */
    public function autoload($class)
    {
        if ($this->_collectClasses) {
            $this->_arrLoadedClasses[self::$_scope][] = $class;
        }

	    $classFile = str_replace(' ', DIRECTORY_SEPARATOR, ucwords(str_replace('_', ' ', $class)));

	    if ($this->_isIncludePathDefined) {
            $classFile =  COMPILER_INCLUDE_PATH . DIRECTORY_SEPARATOR . $class;
        }

        $classFile.= '.php';

        if (stream_resolve_include_path($classFile)) {
            return include $classFile;
        }

        $classFile = ltrim($this->classToFile($class) . '.php', '\\/');
	    if (stream_resolve_include_path($classFile)) {
		    return include $classFile;
	    }

        return false;
    }

    /**
     * Register autoload scope
     * This process allow include scope file which can contain classes
     * definition which are used for this scope
     *
     * @param string $code scope code
     */
    static public function registerScope($code)
    {
        self::$_scope = $code;
        if (defined('COMPILER_INCLUDE_PATH')) {
            @include COMPILER_INCLUDE_PATH . DIRECTORY_SEPARATOR . self::SCOPE_FILE_PREFIX.$code.'.php';
        }
    }

    /**
     * Get current autoload scope
     *
     * @return string
     */
    static public function getScope()
    {
        return self::$_scope;
    }

    /**
     * Class destructor
     */
    public function __destruct()
    {
        if ($this->_collectClasses) {
            $this->_saveCollectedStat();
        }
    }

    /**
     * Save information about used classes per scope with class popularity
     * Class_Name:popularity
     *
     * @return WpLoader
     */
    protected function _saveCollectedStat()
    {
        if (!is_dir($this->_collectPath)) {
            @mkdir($this->_collectPath);
            @chmod($this->_collectPath, 0777);
        }

        if (!is_writeable($this->_collectPath)) {
            return $this;
        }

        foreach ($this->_arrLoadedClasses as $scope => $classes) {
            $file = $this->_collectPath.DIRECTORY_SEPARATOR.$scope.'.csv';
            $data = array();
            if (file_exists($file)) {
                $data = explode("\n", file_get_contents($file));
                foreach ($data as $index => $class) {
                    $class = explode(':', $class);
                    $searchIndex = array_search($class[0], $classes);
                    if ($searchIndex !== false) {
                        $class[1]+=1;
                        unset($classes[$searchIndex]);
                    }
                    $data[$index] = $class[0].':'.$class[1];
                }
            }
            foreach ($classes as $class) {
                $data[] = $class . ':1';
            }
            file_put_contents($file, implode("\n", $data));
        }
        return $this;
    }

    /**
     * Includes a controller given a controller class name
     *
     * @param string $class controller class name
     * @return @link http://www.php.net/manual/en/function.include.php
     */
    private function includeController($class)
    {
        $local = $this->_srcPath . DIRECTORY_SEPARATOR;
        $controller = explode('_', $class);
        array_splice($controller, 2, 0 , 'controllers');
        $pathToController = implode(DIRECTORY_SEPARATOR, $controller);
        $classFile = $local . $pathToController . '.php';
        if (!file_exists($classFile)) {
            return false;
        }
        return include_once $classFile;
    }
}
