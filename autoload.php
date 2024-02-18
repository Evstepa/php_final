<?php

declare(strict_types=1);

/**
 * @param string $classname
 * @return void
 */
function LoadControllers(string $classname): void
{
    $name = 'Controller/' . $classname . '.php';
    if (file_exists($name)) {
        require_once $name;
    }
}

/**
 * @param string $classname
 * @return void
 */
function LoadEntities(string $classname): void
{
    $name = 'Entity/' . $classname . '.php';
    if (file_exists($name)) {
        require_once $name;
    }
}

/**
 * @param string $classname
 * @return void
 */
function LoadRepositories(string $classname): void
{
    $name = 'Repository/' . $classname . '.php';
    if (file_exists($name)) {
        require_once $name;
    }
}

/**
 * @param string $classname
 * @return void
 */
function LoadServices(string $classname): void
{
    $name = 'Service/' . $classname . '.php';
    if (file_exists($name)) {
        require_once $name;
    }
}

spl_autoload_register('LoadControllers');
spl_autoload_register('LoadEntities');
spl_autoload_register('LoadRepositories');
spl_autoload_register('LoadServices');
