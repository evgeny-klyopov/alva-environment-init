<?php

/**
 * Created by PhpStorm.
 * User: Alva
 * Date: 08.06.2017
 * Time: 01:21
 * @author Alva <mail@klepov.info>
 */

namespace Alva\InitEnvironment;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Class App Singleton
 * @package Alva\InitEnvironment
 */
final class App
{
    /**
     * @var object|null Instance this class
     */
    private static $instance;
    /**
     * @var Command
     */
    private $command;
    /**
     * @var OutputInterface
     */
    private $output;
    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var Message
     */
    public $message;
    /**
     * @var string Directory name environment
     */
    public $environment;

    /**
     * @param Command $command object
     * @param InputInterface $input object
     * @param OutputInterface $output object
     * @param $environment string Directory name
     * @return App|null|object
     */
    public static function getInstance(Command $command, InputInterface $input, OutputInterface $output, $environment)
    {
        if (null === self::$instance) {
            self::$instance = new self($command, $input, $output, $environment);
        }

        return self::$instance;
    }

    /**
     * App constructor.
     * @param Command $command object
     * @param InputInterface $input object
     * @param OutputInterface $output object
     * @param $environment string Directory name
     */
    private function __construct(Command $command, InputInterface $input, OutputInterface $output, $environment)
    {
        $this->command = $command;
        $this->output = $output;
        $this->input = $input;
        $this->environment = preg_replace('/[^\p{L}-_0-9]/u', '', $environment);
        //
        $this->message = Message::getInstance();

        // start message
        $this->writeIn($command->getName(), 'green');
    }

    /**
     * Method closing for Singleton
     */
    private function __clone()
    {

    }

    /**
     * Method closing for Singleton
     */
    private function __wakeup()
    {

    }

    /**
     * Create directory
     * @param $path string Absolute path to the directory created
     */
    private function createDirectory($path)
    {
        $fs = new Filesystem();

        try {
            if (!$fs->exists($path)) {
                $fs->mkdir($path);
                $this->writeIn('Create directory', 'cyan', ['directory' => $path]);
            }
        } catch (IOExceptionInterface $e) {
            $this->writeIn(
                'An error occurred while creating your directory',
                'red',
                [
                    'directory' => $path,
                    'error' => $e->getPath()
                ]
            );
        }
    }

    /**
     * Write message "complete", after successful execution
     */
    public function __destruct()
    {
        // end message
        $this->writeIn('complete', 'yellow');
    }


    /**
     * Write message
     * @param $message string Message
     * @param bool $color string|bool Color (white, red, etc)
     * @param array $params array Array to the placeholder, key replace value
     */
    public function writeIn($message, $color = false, array $params = [])
    {
       $this->output->writeln(
           $this->message->formatMessage($message, $color, $params)
       );
    }

    /**
     * Create directory for environments
     * @return $this
     */
    public function setStorage() {
        $this->createDirectory(PATH .DS . ENVIRONMENT_DIRECTORY_NAME. DS . $this->environment);

        return $this;
    }

    /**
     * Copies a file
     * @param $originFile string Path the original filename
     * @param $targetFile string Path the target filename
     * @return $this
     */
    public function copyFile($originFile, $targetFile, $overwrite = false)
    {
        $fs = new Filesystem();

        if (!is_file(PATH . DS . $originFile)) {
            $this->writeIn('Not found file', 'red', ['filePath' => $originFile]);
        } else {
            $absolutePathTargetFile = PATH . DS . $targetFile;

            // check file, overwrite default
            if (false == $overwrite && is_file($absolutePathTargetFile)) {
                $helper = $this->command->getHelper('question');

                $question = new ConfirmationQuestion(
                    $this->message->formatMessage(
                        'File exists',
                        'red',
                        ['filePath' => $targetFile]
                    ),
                    true
                );

                // if selected no, return
                if (!$helper->ask($this->input, $this->output, $question)) {
                    return $this;
                }
            }


            try {
                $fs->copy(PATH . DS .$originFile,  $absolutePathTargetFile, true);
                $this->writeIn(
                    'File is saved',
                    'green',
                    ['originFile' => $originFile, 'targetFile' => $targetFile]
                );
            } catch (IOExceptionInterface $e) {
                $this->writeIn(
                    'Error copy file',
                    'red',
                    ['absolutePathTargetFile' => $absolutePathTargetFile, 'error' => $e->getPath()]
                );
            }
        }

        return $this;
    }

    /**
     * Add filepath to file .gitignore
     * @param $filePath
     * @return $this
     */
    public function addFileToGitignore($filePath)
    {
        $fs = new Filesystem();

        if (is_file(FILE_GIT_IGNORE)) {
            $content = file(FILE_GIT_IGNORE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            $search = [
                str_replace('/', '\\', $filePath),
                str_replace('\\', '/', $filePath)
            ];

            $found = 0;
            foreach ($content as $key => $value) {
                if (in_array($content[$key], $search)) {
                    unset($content[$key]);
                    $found = 1;
                }
            }

            if (1 == $found) {
                $this->writeIn(
                    'File exist in .gitignore',
                    'green',
                    ['filePath' => $filePath, 'gitIgnorePath' => FILE_GIT_IGNORE]
                );
                return $this;
            }
        }

        try {
            $fs->appendToFile(FILE_GIT_IGNORE, PHP_EOL . $filePath);
            $this->writeIn(
                'Add file to .gitignore',
                'green',
                ['filePath' => $filePath, 'gitIgnorePath' => FILE_GIT_IGNORE]
            );
        } catch (IOExceptionInterface $e) {
            $this->writeIn(
                'Error add file to .gitignore',
                'red',
                ['filePath' => $filePath, 'gitIgnorePath' => FILE_GIT_IGNORE]
            );
        }

        return $this;
    }

    /**
     * Cut first slash and convert slashes to OS delimiter
     * @param $filePath string Path the file
     * @return string
     */
    public function normalizeFilePath($filePath) {
        return preg_replace(
            '/^(\/|\\\\)/',
            '',
            str_replace(['/', '\\'], [DS, DS], $filePath)
        );
    }


    /**
     * Remove filepath in file .gitignore
     * @param $filePath
     * @return $this
     */
    public function removeFileInGitignore($filePath)
    {
        if (is_file(FILE_GIT_IGNORE)) {
            $content = file(FILE_GIT_IGNORE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            try {
                $search = [
                    str_replace('/', '\\', $filePath),
                    str_replace('\\', '/', $filePath)
                ];

                $found = 0;
                foreach ($content as $key => $value) {
                    if (in_array($content[$key], $search)) {
                        unset($content[$key]);
                        $found = 1;
                    }
                }

                if (1 == $found) {
                    $dir = dirname($filePath);

                    if (!is_writable($dir)) {
                        throw new IOException(
                            sprintf('Unable to write to the "%s" directory.', $dir),
                            0,
                            null,
                            $dir
                        );
                    }

                    if (false === @file_put_contents(FILE_GIT_IGNORE, implode(PHP_EOL, $content))) {
                        throw new IOException(
                            sprintf('Failed to write file "%s".', FILE_GIT_IGNORE),
                            0,
                            null,
                            FILE_GIT_IGNORE
                        );
                    }
                } else {
                    $this->writeIn(
                        'File not exist in .gitignore',
                        'red',
                        ['filePath' => $filePath, 'gitIgnorePath' => FILE_GIT_IGNORE]
                    );
                    return $this;
                }
            } catch (IOExceptionInterface $e) {
                $this->writeIn(
                    'Error add file to .gitignore',
                    'red',
                    ['filePath' => $filePath, 'gitIgnorePath' => FILE_GIT_IGNORE]
                );
            }
        } else {
            $this->writeIn(
                'File .gitignore not found',
                'red',
                ['gitIgnorePath' => FILE_GIT_IGNORE]
            );
        }

        return $this;
    }

    /**
     * @param $filePath string
     * @param $environment string Directory environment
     * @param bool $removeEmptyDirectory bool Flag remove empty directories
     * @return $this
     */
    public function removeFile($filePath, $environment, $removeEmptyDirectory = true)
    {
        $fs = new Filesystem();

        $remove = function($filePath) use ($fs){
            try {
                $fs->remove(PATH . DS . $filePath);
                $this->writeIn(
                    'File is remove',
                    'green',
                    ['filePath' => $filePath]
                );
            } catch (IOExceptionInterface $e) {
                $this->writeIn(
                    'Error remove file',
                    'red',
                    ['filePath' => $filePath, 'error' => $e->getPath()]
                );
            }
        };


        if (!is_file(PATH . DS . $filePath)) {
            $this->writeIn('Not found file', 'red', ['filePath' => $filePath]);
        } else {
            // remove file
            $remove($filePath);
        }

        // remove empty directories
        if (true == $removeEmptyDirectory) {
            //remove empty directory
            // explode path to dirs
            $directories = array_diff(
                explode(
                    DS,
                    // cut ENVIRONMENT_DIRECTORY_NAME
                    str_replace(ENVIRONMENT_DIRECTORY_NAME, '', $filePath)
                ),
                ['']
            );

            // cut environment directory
            array_shift($directories);
            // cut filename
            array_pop($directories);

            $countDirectories = count($directories);
            if ($countDirectories > 0) {
                for ($i = $countDirectories; $i >= 0; $i--) {
                    $projectDirectoryPath = ENVIRONMENT_DIRECTORY_NAME
                        . DS
                        . $environment
                        . DS
                        . implode(DS, $directories);
                    $directory = PATH . DS . $projectDirectoryPath;

                    if (is_dir($directory)) {
                        if (!(new \FilesystemIterator($directory))->valid() == false) { // is not empty directory
                            break;
                        } else {
                            $remove($projectDirectoryPath);
                            array_pop($directories);
                        }
                    }
                }
            }
        }

        return $this;
    }
}
