<?php

namespace LeMike\PhpSpec\WpExtension\Locator\WordPress;

use PhpSpec\Locator\ResourceLocatorInterface;
use PhpSpec\Util\Filesystem;

abstract class AbstractResourceLocator
{
    protected $classType;
    protected $validator;
    protected $srcPath;
    protected $specPath;
    protected $srcNamespace;
    protected $specNamespace;
    protected $fullSrcPath;
    protected $fullSpecPath;
    protected $filesystem;
    protected $codePool;

    public function getFullSrcPath()
    {
        return $this->fullSrcPath;
    }

    public function getFullSpecPath()
    {
        return $this->fullSpecPath;
    }

    public function getSrcNamespace()
    {
        return $this->srcNamespace;
    }

    public function getSpecNamespace()
    {
        return $this->specNamespace;
    }

    public function getAllResources()
    {
        return $this->findSpecResources($this->fullSpecPath);
    }

    public function supportsQuery($query)
    {
        $isSupported = (bool) preg_match($this->validator, $query) || $this->isSupported($query);;

        return $isSupported;
    }

    public function findResources($query)
    {
        $path = rtrim(realpath(str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $query)), DIRECTORY_SEPARATOR);

        if ('.php' !== substr($path, -4)) {
            $path .= DIRECTORY_SEPARATOR;
        }

        if ($path && 0 === strpos($path, $this->fullSrcPath)) {
            $path = $this->fullSpecPath.substr($path, strlen($this->fullSrcPath));
            $path = preg_replace('/\.php/', 'Spec.php', $path);

            return $this->findSpecResources($path);
        }

        if ($path && 0 === strpos($path, $this->srcPath)) {
            $path = $this->fullSpecPath.substr($path, strlen($this->srcPath));
            $path = preg_replace('/\.php/', 'Spec.php', $path);

            return $this->findSpecResources($path);
        }

        if ($path && 0 === strpos($path, $this->specPath)) {
            return $this->findSpecResources($path);
        }

        return array();
    }

    public function supportsClass($classname)
    {
	    return true;
        $parts = explode('_', $classname);

        if (count($parts) < 2) {
            return false;
        }

        return (
            $this->supportsQuery($classname) ||
            $classname === implode('_', array($parts[0], $parts[1], $this->classType, $parts[count($parts)-1]))
        );
    }

    public function createResource($classname)
    {
        return $this->getResource(explode('\\', $classname), $this);
    }

    abstract public function getPriority();

    /**
     * @param string $path
     */
    protected function findSpecResources($path)
    {
        if (!$this->filesystem->pathExists($path)) {
            return array();
        }

        if ('.php' === substr($path, -4)) {
            if (!$this->isSupported($path)) {
                return array();
            }

            return array($this->createResourceFromSpecFile(realpath($path)));
        }

        $resources = array();
        foreach ($this->filesystem->findSpecFilesIn($path) as $file) {
            $specFile = $file->getRealPath();
            if ($this->isSupported($specFile)) {
                $resources[] = $this->createResourceFromSpecFile($specFile);
            }
        }

        return $resources;
    }

    private function createResourceFromSpecFile($path)
    {
        // cut "Spec.php" from the end
        $relative = $this->getRelative($path);

        return $this->getResource(explode(DIRECTORY_SEPARATOR, $relative), $this);
    }

    private function checkInitialData()
    {
        if (null === $this->classType) {
            throw new \UnexpectedValueException('Concrete resource locators mist specify a class type');
        }

        if (null === $this->validator) {
            throw new \UnexpectedValueException('Concrete resource locators mist specify a validation rule');
        }
    }

    protected function getClassnameFromMatches(array $matches)
    {
        $vendor = ucfirst(array_shift($matches));
        $module = ucfirst(array_shift($matches));

        $objectName = implode('_', array_map('ucfirst', explode('_', implode('', $matches))));
        return implode('_', array($vendor, $module, $this->classType, $objectName));
    }

    protected function getRelative($path)
    {
        // cut "Spec.php" from the end
        $relative = substr($path, strlen($this->fullSpecPath), -4);
        return preg_replace('/Spec$/', '', $relative);
    }

    abstract protected function isSupported($file);

    abstract protected function getResource(array $parts, ResourceLocatorInterface $locator);
}