<?php
/**
 * Created by PhpStorm.
 * User: Alva
 * Date: 08.06.2017
 * Time: 03:15
 */

return [
    // class Alva\InitEnvironment\Console\Add
    // configure
    'Description action add' => 'Добавление нового файла',
    'Argument environment in action add' => 'Название площадки (production, local, etc), обязательный аргумент',
    'Argument filePath in action add' => 'Путь до файла от константы PATH ("' . PATH . '"),' . PHP_EOL
        . ' передается в ковычках, слеши автоматически приводятся к виду "' . DS . '"' . PHP_EOL
        . '(пример: "vendor/symfony/filesystem/Filesystem.php"), обязательный аргумент',
    'Argument addFileToGitignore in action add' => 'Булевый флаг (1, 0) добавление файла в .gitignore' . PHP_EOL
        . '(описан в константе FILE_GIT_IGNORE ("' . FILE_GIT_IGNORE . '"),' . PHP_EOL
        . 'по умолчанию 0, опциональный аргумент.',
    'How use (add help)?' => 'php init app:add production "path/to/file.php"' . PHP_EOL
        . 'php init app:add production "path/to/file.php 1' . PHP_EOL
        . 'php init app:add production "path/to/file.php 0',

    // execute
    'Add file' => 'Добавление файла - {filePath}',
    'Add file to .gitignore?' => 'Добавить файл {filePath} в .gitignore (по умолчанию no)? <fg=yellow>[yes | no]</> : ',
    // end class Alva\InitEnvironment\Console\Add


    // class Alva\InitEnvironment\Console\Remove
    // configure
    'Description action remove' => 'Удаление файла',
    'Argument environment in action remove' => 'Название площадки (production, local, etc), обязательный аргумент',
    'Argument filePath in action remove' => 'Путь до файла от константы PATH ("' . PATH . '"),' . PHP_EOL
        . ' передается в ковычках, слеши автоматически приводятся к виду "' . DS . '"' . PHP_EOL
        . '(пример: "vendor/symfony/filesystem/Filesystem.php"), обязательный аргумент',
    'Argument removeFileInGitIgnore in action remove' => 'Булевый флаг (1, 0) удаление файла из .gitignore' . PHP_EOL
        . '(описан в константе FILE_GIT_IGNORE ("' . FILE_GIT_IGNORE . '"),' . PHP_EOL
        . 'по умолчанию 0, опциональный аргумент.',
    'Argument removeInAllEnvironment in action remove' => 'Булевый флаг (1, 0) удаление файла из всех площадок (environment)' . PHP_EOL
        . 'по умолчанию 0, опциональный аргумент.',
    'How use (remove help)?' => 'php init app:remove production "path/to/file.php"' . PHP_EOL
        . 'php init app:remove production "path/to/file.php 1 0' . PHP_EOL
        . 'php init app:remove production "path/to/file.php 0 1' . PHP_EOL
        . 'php init app:remove production "path/to/file.php 1 1' . PHP_EOL
        . 'php init app:remove production "path/to/file.php 0 0',

    // execute
    'Remove file in the .gitignore?' => 'Удалить файл {filePath} из .gitignore (по умолчанию no)? <fg=yellow>[yes | no]</> : ',
    'Remove file in all the environment?' => 'Удалить файл {filePath} из всех площадок (по умолчанию no)? <fg=yellow>[yes | no]</> : ',
    // end class Alva\InitEnvironment\Console\Remove


    // class Alva\InitEnvironment\Console\ShowList
    // configure
    'Description action show list' => 'Показ добавленных файлов',
    'Argument environment in action show list' => 'Название площадки (production, local, etc), опциональный аргумент',
    'How use (show list help)' => 'php init app:show-list' . PHP_EOL
        . 'php init app:show-list production' . PHP_EOL,

    // execute
    'Argument environment is not set or exists' => 'Аргумент не environment не установлен или директория не существует',
    'Name environment' => 'Название площадки: {environment}',
    'Files environment' => '- {filePath}',
    // end class Alva\InitEnvironment\Console\ShowList


    // class Alva\InitEnvironment\Console\Load
    // configure
    'Description action load' => 'Загрузка файлов',
    'Argument environment in action load' => 'Название площадки (production, local, etc), обязательный аргумент',
    'Argument overwriteAll in action load' => 'Флаг замены всех файлов, опциональный аргумент',
    'How use (load help)?' => 'php init app:load' . PHP_EOL
        . 'php init app:load production 1' . PHP_EOL,

    // execute
    'Overwrite all files?' => 'Перезаписать все файлы (по умолчанию no)? <fg=yellow>[yes | no]</> : ',
    // end class Alva\InitEnvironment\Console\Load


    // class Alva\InitEnvironment\App
    // __construct
    'app:add' => 'Добавление файла',
    'app:remove' => 'Удаление файла',

    // __desctruct
    'complete' => 'Завершено',

    // createDirectory
    'Create directory' => 'Создание директории {directory}',
    'An error occurred while creating your directory' => 'Произошла ошибка при создании каталога - {directory}, ошибка - {error}',

    // copyFile
    'Not found file' => 'Не найден файл - {filePath}',
    'File exists' => 'Файл {filePath} уже существует, заменить его (по умолчанию yes)? <fg=yellow>[yes | no]</> : ',
    'Error copy file' => 'Произошла ошибка при копирование файла - {absolutePathTargetFile}, ошибка - {error}',
    'File is saved' => 'Файл сохранен {originFile} в {targetFile}',

    // addFileToGitignore
    'File exist in .gitignore' => 'Файл ({filePath}) уже добавлен в .gitignore ({gitIgnorePath})',
    'Add file to .gitignore' => 'Добавление файла ({filePath}) в .gitignore ({gitIgnorePath})',
    'Error add file to .gitignore' => 'Ошибка добавления файла ({filePath}) в .gitignore ({gitIgnorePath})',

    // removeFileInGitignore
    'File not exist in .gitignore' => 'Файл ({filePath}) не найден в .gitignore ({gitIgnorePath})',
    'File .gitignore not found' => 'Файл .gitignore ({gitIgnorePath}) не найден',
    'Error remove file' => 'Произошла ошибка при удаление файла - {filePath}, ошибка - {error}',
    'File is remove' => 'Файл {filePath} удален',
    // end class Alva\InitEnvironment\App



];