<?php

namespace LeMike\PhpSpec\WpExtension\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class WpCommand extends Command
{
    /**
     * @var string
     */
    protected $validator;

    /**
     * @var string
     */
    protected $help;

    /**
     * @var string
     */
    protected $type;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $className = $input->getArgument('alias');

        if (!$className) {
            throw new \InvalidArgumentException($this->help);
        }

        $container = $this->getApplication()->getContainer();
        $container->configure();

        $resource  = $container->get('locator.resource_manager')->createResource($className);

        $container->get('code_generator')->generate($resource, 'specification');
    }
} 