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
 * Class Remove
 * @package Alva\InitEnvironment\Console
 */
class Remove extends Command
{
    /**
     *  Configure command
     */
    protected function configure()
    {
        $message = Message::getInstance();

        $this
            ->setName('app:remove')
            ->setDescription($message->getMessage('Description action remove'))
            ->addArgument(
                'environment',
                InputArgument::REQUIRED,
                $message->getMessage('Argument environment in action remove')
            )
            ->addArgument(
                'filePath',
                InputArgument::REQUIRED,
                $message->getMessage('Argument filePath in action remove')
            )
            ->addArgument(
                'removeFileInGitIgnore',
                InputArgument::OPTIONAL,
                $message->getMessage('Argument removeFileInGitIgnore in action remove')
            )
            ->addArgument(
                'removeInAllEnvironment',
                InputArgument::OPTIONAL,
                $message->getMessage('Argument removeInAllEnvironment in action remove')
            )
            ->setHelp($message->getMessage('How use (remove help)?'))
        ;
    }

    /**
     * Remove file to storage environment
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null|int null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // get argument
        $environment = $input->getArgument('environment');
        $filePath = $input->getArgument('filePath');
        $removeFileInGitIgnore = $input->getArgument('removeFileInGitIgnore');
        $removeInAllEnvironment = $input->getArgument('removeInAllEnvironment');

        // init application, set params and add file
        $app = App::getInstance($this, $input, $output, $environment)
            ->setStorage();

        // Cut first slash and convert slashes to OS delimiter
        $filePath = $app->normalizeFilePath($filePath);

        $question = function(&$flag, $messageType, $params) use ($app, $input, $output) {
            if (is_null($flag)) {
                $helper = $this->getHelper('question');
                $question = new ConfirmationQuestion(
                    $app->message->formatMessage(
                        $messageType,
                        'red',
                        $params
                    ),
                    false
                );

                // if the answer is "yes", set the flag to add
                if ($helper->ask($input, $output, $question)) {
                    $flag = 1;
                }
            }
        };

        $question($removeFileInGitIgnore, 'Remove file in the .gitignore?', ['filePath' => $filePath]);
        // remove filePath in .gitignore
        if (1 == $removeFileInGitIgnore) {
            $app->removeFileInGitignore($filePath);
        }

        $question($removeInAllEnvironment, 'Remove file in all the environment?', ['filePath' => $filePath]);

        if (1 == $removeInAllEnvironment) {
            $finder = new Finder();
            $finder->directories()
                ->in(PATH . DS . ENVIRONMENT_DIRECTORY_NAME)
                ->depth(0);
            foreach ($finder as $directory) {
                $app->removeFile(ENVIRONMENT_DIRECTORY_NAME . DS . $directory->getFilename() . DS . $filePath, $directory->getFilename(), true);
            }
        } else {
            $app->removeFile($app->environment. DS . $filePath, $app->environment, true);
        }
    }
}