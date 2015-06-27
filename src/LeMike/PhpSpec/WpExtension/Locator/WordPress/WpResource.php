<?php

namespace LeMike\PhpSpec\WpExtension\Locator\WordPress;

use LeMike\PhpSpec\WpExtension\Autoloader\WpLoader;
use PhpSpec\Locator\ResourceInterface;

class WpResource implements ResourceInterface
{
    private $parts;
    private $locator;

    public function __construct(array $parts, WpLocator $locator)
    {
        $this->parts   = $parts;
        $this->locator = $locator;
    }

	/**
	 * @return mixed
	 */
	public function getName()
	{
		return end($this->parts);
	}

	/**
	 * @return string
	 */
	public function getSpecName()
	{
		return $this->getName().'Spec';
	}

	/**
	 * @return string
	 */
	public function getSrcFilename()
	{
		return $this->locator->getFullSrcPath()
		       . WpLoader::classToFile( implode('\\', $this->parts)) . '.php';
	}

	/**
	 * @return string
	 */
	public function getSrcNamespace()
	{
		$nsParts = $this->parts;
		array_pop($nsParts);

		return rtrim($this->locator->getSrcNamespace().implode('\\', $nsParts), '\\');
	}

	/**
	 * @return string
	 */
	public function getSrcClassname()
	{
		return $this->locator->getSrcNamespace().implode('\\', $this->parts);
	}

	/**
	 * @return string
	 */
	public function getSpecFilename()
	{
		$nsParts   = $this->parts;
		$classname = array_pop($nsParts);
		$parts     = array_merge($nsParts, explode('_', $classname));

		return $this->locator->getFullSpecPath().
		       implode(DIRECTORY_SEPARATOR, $parts).'Spec.php';
	}

	/**
	 * @return string
	 */
	public function getSpecNamespace()
	{
		$nsParts = $this->parts;
		array_pop($nsParts);

		return rtrim($this->locator->getSpecNamespace().implode('\\', $nsParts), '\\');
	}

	/**
	 * @return string
	 */
	public function getSpecClassname()
	{
		return $this->locator->getSpecNamespace().implode('\\', $this->parts).'Spec';
	}
}