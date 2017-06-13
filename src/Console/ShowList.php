<?php
/**
 * Created by PhpStorm.
 * User: Alva
 * Date: 08.06.2017
 * Time: 02:10
 */

namespace Alva\InitEnvironment\Console;

use Alva\InitEnvironment\App;
use Alva\InitEnvironment\Message;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * Class ShowList
 * @package Alva\InitEnvironment\Console
 */
class ShowList extends Command
{
    /**
     *  Configure command
     */
    protected function configure()
    {
        $message = Message::getInstance();

        $this
            ->setName('app:show-list')
            ->setDescription($message->getMessage('Description action show list'))
            ->addArgument(
                'environment',
                InputArgument::OPTIONAL,
                $message->getMessage('Argument environment in action show list')
            )
            ->setHelp($message->getMessage('How use (show list help)?'))
        ;
    }

    /**
     * Show list files for storage environment
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null|int null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // get argument
        $environment = $input->getArgument('environment');

        // init application, set params and add file
        $app = App::getInstance($this, $input, $output, $environment);

        $searchIn = PATH . DS . ENVIRONMENT_DIRECTORY_NAME;
        $finder = (new Finder())->in($searchIn);

        if ($app->environment && is_dir($searchIn . DS . $app->environment)) {
            $finder->path($app->environment);
        } else {
            $app->writeIn('Argument environment is not set or exists', 'cyan');
        }

        $finder = (new Finder())->in($searchIn)->path($app->environment);
        foreach ($finder as $file) {
            $name = $file->getRelativePathname();

            if (false === strpos($name, DS)) {
                $app->writeIn('Name environment', 'green', ['environment' => $name]);
            } else {
                $app->writeIn('Files environment', 'cyan', ['filePath' => $name]);
            }
        }
    }
}