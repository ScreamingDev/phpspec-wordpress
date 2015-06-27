<?php

namespace LeMike\PhpSpec\WpExtension\Extension;


use LeMike\PhpSpec\WpExtension\Console\Command\DescribeBlockCommand;
use LeMike\PhpSpec\WpExtension\Console\Command\DescribeControllerCommand;
use LeMike\PhpSpec\WpExtension\Console\Command\DescribeHelperCommand;
use LeMike\PhpSpec\WpExtension\Console\Command\DescribeModelCommand;
use LeMike\PhpSpec\WpExtension\Console\Command\DescribeResourceModelCommand;
use LeMike\PhpSpec\WpExtension\Console\Command\DescribeWpCommand;
use PhpSpec\ServiceContainer;

class CommandAssembler implements Assembler
{
    /**
     * @param ServiceContainer $container
     */
    public function build(ServiceContainer $container)
    {
        $container->setShared('console.commands.describe_wp', function ($c) {
            return new DescribeWpCommand();
        });
    }
} 