<?php
/**
 * Created by PhpStorm.
 * User: Alva
 * Date: 08.06.2017
 * Time: 01:10
 */

namespace Alva\InitEnvironment\Console;

use Alva\InitEnvironment\App;
use Alva\InitEnvironment\Message;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;


/**
 * Class Add
 * @package Alva\InitEnvironment\Console
 */
class Add extends Command
{
    /**
     *  Configure command
     */
    protected function configure()
    {
        $message = Message::getInstance();

        $this
            ->setName('app:add')
            ->setDescription($message->getMessage('Description action add'))
            ->addArgument(
                'environment',
                InputArgument::REQUIRED,
                $message->getMessage('Argument environment in action add')
            )
            ->addArgument(
                'filePath',
                InputArgument::REQUIRED,
                $message->getMessage('Argument filePath in action add')
            )
            ->addArgument(
                'addFileToGitignore',
                InputArgument::OPTIONAL,
                $message->getMessage('Argument addFileToGitignore in action add')
            )
            ->setHelp($message->getMessage('How use (add help)?'))
        ;
    }

    /**
     * Add file to storage environment
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null|int null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // get argument
        $environment = $input->getArgument('environment');
        $filePath = $input->getArgument('filePath');
        $addFileToGitignore = $input->getArgument('addFileToGitignore');

        // init application, set params and add file
        $app = App::getInstance($this, $input, $output, $environment)
            ->setStorage();

        // Cut first slash and convert slashes to OS delimiter
        $filePath = $app->normalizeFilePath($filePath);

        $app->copyFile(
            $filePath,
            ENVIRONMENT_DIRECTORY_NAME . DS . $app->environment . DS . $filePath
        );

        // if argument addGitIgnore no passed,
        // ask a question about adding a file to .gitignore
        if (is_null($addFileToGitignore)) {
            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion(
                $app->message->formatMessage(
                    'Add file to .gitignore?',
                    'red',
                    ['filePath' => $filePath]
                ),
                false
            );

            // if the answer is "yes", set the flag to add
            if ($helper->ask($input, $output, $question)) {
                $addFileToGitignore = 1;
            }
        }

        // add to .gitignore
        if(1 == $addFileToGitignore) {
            $app->addFileToGitignore($filePath);
        }
    }
}
