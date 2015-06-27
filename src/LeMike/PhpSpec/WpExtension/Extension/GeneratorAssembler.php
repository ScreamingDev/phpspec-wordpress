<?php

namespace LeMike\PhpSpec\WpExtension\Extension;

use LeMike\PhpSpec\WpExtension\CodeGenerator\Generator\BlockGenerator;
use LeMike\PhpSpec\WpExtension\CodeGenerator\Generator\ControllerGenerator;
use LeMike\PhpSpec\WpExtension\CodeGenerator\Generator\ControllerSpecificationGenerator;
use LeMike\PhpSpec\WpExtension\CodeGenerator\Generator\HelperGenerator;
use LeMike\PhpSpec\WpExtension\CodeGenerator\Generator\SpecificationGenerator;
use LeMike\PhpSpec\WpExtension\CodeGenerator\Generator\WpGenerator;
use LeMike\PhpSpec\WpExtension\CodeGenerator\Generator\ResourceModelGenerator;
use PhpSpec\ServiceContainer;

class GeneratorAssembler implements Assembler
{
    /**
     * @param ServiceContainer $container
     */
    public function build(ServiceContainer $container)
    {
        $this->setCodeGenerators($container);
    }

    private function setCodeGenerators(ServiceContainer $container)
    {
        $container->setShared('code_generator.generators.wp_class', function ($c) {
            return new WpGenerator(
                $c->get('console.io'),
                $c->get('code_generator.templates'),
                $c->get('filesystem')
            );
        });

	    $container->setShared('code_generator.generators.wp_specification', function ($c) {
		    return new SpecificationGenerator(
			    $c->get('console.io'),
			    $c->get('code_generator.templates'),
			    $c->get('filesystem')
		    );
	    });
    }
}