<?php

namespace LeMike\PhpSpec\WpExtension\Extension;

use PhpSpec\ServiceContainer;

interface Assembler
{
    /**
     * @param ServiceContainer $container
     */
    public function build(ServiceContainer $container);
} 