<?php

namespace LeMike\PhpSpec\WpExtension\Console\Command;

use Symfony\Component\Console\Input\InputArgument;

class DescribeWpCommand extends WpCommand
{
    /**
     * @var string
     */
    protected $validator = '/^([a-zA-Z0-9]+)_([a-zA-Z0-9]+)\/([a-zA-Z0-9]+)(_[\w]+)?$/';

    /**
     * @var string
     */
    protected $help = <<<HELP
Please provide a class name like "LeMike\\Dev".
HELP;

    /**
     * @var string
     */
    protected $type = 'model';

    protected function configure()
    {
        $this
            ->setName('describe:wp')
            ->setDescription('Describe a WordPress-Class specification')
            ->addArgument('alias', InputArgument::REQUIRED, 'WordPress Class to be described');
    }
}
