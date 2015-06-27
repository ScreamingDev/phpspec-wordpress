<?php

namespace LeMike\PhpSpec\WpExtension\Extension;

use LeMike\PhpSpec\WpExtension\Locator\WordPress\WpLocator;
use PhpSpec\ServiceContainer;

class LocatorAssembler implements Assembler
{

    /**
     * @param ServiceContainer $container
     */
    public function build(ServiceContainer $container)
    {
        $assembler = $this;
        $container->addConfigurator(function ($c) use ($assembler) {
            $config = $c->getParam('wp_locator', array('main' => ''));

            $srcNS = $assembler->getNamespace($config);
            $specPrefix = $assembler->getSpecPrefix($config);
            $srcPath = $assembler->getSrcPath($config);
            $specPath = $assembler->getSpecPath($config);
            $filesystem = $c->get('filesystem');

            if (!$filesystem->isDirectory($srcPath)) {
                $filesystem->makeDirectory($srcPath);
            }

            if (!$filesystem->isDirectory($specPath)) {
                $filesystem->makeDirectory($specPath);
            }

            $c->setShared('locator.locators.wp_locator',
                function ($c) use ($srcNS, $specPrefix, $srcPath, $specPath, $filesystem) {
                    return new WpLocator($srcNS, $specPrefix, $srcPath, $specPath, $filesystem);
                }
            );
        });
    }

    public function getNamespace(array $config)
    {
        return array_key_exists('namespace', $config) ? $config['namespace'] : '';
    }

    public function getSpecPrefix(array $config)
    {
        return array_key_exists('spec_prefix', $config) ? $config['spec_prefix'] : '';
    }

    public function getSrcPath(Array $config)
    {
        return array_key_exists('src_path', $config) ? rtrim($config['src_path'], '/') . DIRECTORY_SEPARATOR : 'src';
    }

    public function getSpecPath(array $config)
    {
        return array_key_exists('spec_path', $config) ? rtrim($config['spec_path'], '/') . DIRECTORY_SEPARATOR : '.';
    }
}