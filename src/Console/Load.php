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
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Finder\Finder;

/**
 * Class Load
 * @package Alva\InitEnvironment\Console
 */
class Load extends Command
{
    /**
     *  Configure command
     */
    protected function configure()
    {
        $message = Message::getInstance();

        $this
            ->setName('app:load')
            ->setDescription($message->getMessage('Description action load'))
            ->addArgument(
                'environment',
                InputArgument::REQUIRED,
                $message->getMessage('Argument environment in action load')
            )
            ->addArgument(
                'overwriteAll',
                InputArgument::OPTIONAL,
                $message->getMessage('Argument overwriteAll in action load')
            )
            ->setHelp($message->getMessage('How use (load help)?'))
        ;
    }

    /**
     * Load files environment to project
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null|int null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // get argument
        $environment = $input->getArgument('environment');
        $overwriteAll = $input->getArgument('overwriteAll');
        //$overwriteAll = (1 == $overwriteAll) ? 1 : 0;

        // init application, set params and add file
        $app = App::getInstance($this, $input, $output, $environment);

        $searchIn = PATH . DS . ENVIRONMENT_DIRECTORY_NAME;

        if (!is_dir($searchIn . DS . $app->environment)) {
            $app->writeIn('Argument environment is not set or exists', 'red');
            return false;
        } else {
            $searchIn .= DS . $app->environment;
            if (is_null($overwriteAll)) {
                $helper = $this->getHelper('question');
                $question = new ConfirmationQuestion(
                    $app->message->formatMessage(
                        'Overwrite all files?',
                        'red'
                    ),
                    false
                );

                // if the answer is "yes", set the flag to add
                if ($helper->ask($input, $output, $question)) {
                    $overwriteAll = 1;
                }
            }

            $overwriteAll = (1 != $overwriteAll ? 0 : $overwriteAll);

            $finder = (new Finder())->in($searchIn)->files();

            foreach ($finder as $file) {
                $originalFile = ENVIRONMENT_DIRECTORY_NAME . DS . $app->environment . DS . $file->getRelativePathname();
                $targetFile = $file->getRelativePathname();

                $app->copyFile($originalFile, $targetFile, $overwriteAll);
            }
        }
    }
}