<?php
/**
 * WPSpec
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License, that is bundled with this
 * package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 *
 * http://opensource.org/licenses/MIT
 *
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world-wide-web, please send an email
 * to <pretzlaw@gmail.com> so we can send you a copy immediately.
 *
 * @category   LeMike
 * @package    PhpSpec_WPExtension
 *
 * @copyright  Copyright (c) 2015 Mike Pretzlaw and contributors.
 */
namespace LeMike\PhpSpec\WpExtension;

use LeMike\PhpSpec\WpExtension\Extension\CommandAssembler;
use LeMike\PhpSpec\WpExtension\Extension\GeneratorAssembler;
use LeMike\PhpSpec\WpExtension\Extension\LocatorAssembler;
use PhpSpec\Extension\ExtensionInterface;
use PhpSpec\ServiceContainer;
use LeMike\PhpSpec\WpExtension\Autoloader\WpLoader;
use PhpSpec\Util\Filesystem;

class Extension implements ExtensionInterface
{
    public function load(ServiceContainer $container)
    {
        $this->setCommands($container);
        $this->setFilesystem($container);
        $this->setGenerators($container);
        $this->setLocators($container);
        $this->configureAutoloader($container);
    }

    private function setCommands(ServiceContainer $container)
    {
        $commandAssembler = new CommandAssembler();
        $commandAssembler->build($container);
    }

    private function setFilesystem(ServiceContainer $container)
    {
        $container->setShared('filesystem', function() {
            return new Filesystem();
        });
    }

    private function setGenerators(ServiceContainer $container)
    {
        $generatorAssembler = new GeneratorAssembler();
        $generatorAssembler->build($container);
    }

    private function setLocators(ServiceContainer $container)
    {
        $locatorAssembler = new LocatorAssembler();
        $locatorAssembler->build($container);
    }

    private function configureAutoloader($container)
    {
        $container->addConfigurator(function ($c) {
            $suite = $c->getParam('wp_locator', array('main' => ''));
            WpLoader::register(
                isset($suite['src_path']) ? rtrim($suite['src_path'], '/') . DIRECTORY_SEPARATOR : 'src',
                isset($suite['code_pool']) ? $suite['code_pool'] : 'local'
            );
        });
    }
}
