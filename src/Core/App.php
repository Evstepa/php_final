<?php

declare(strict_types=1);

namespace App\Core;

class App
{
    public static $_storage = [];

    private static function setStorage(): array
    {
        $searchRoot = $_SERVER["DOCUMENT_ROOT"] . "/src";
        $fileName = '.php';
        $searchResult = [];
        self::searchFiles($searchRoot, $fileName, $searchResult);
        $searchResult = array_filter($searchResult, "filesize");

        $serveces = [];
        foreach ($searchResult as $key => $item) {
            $itemArray = explode('/', $item);
            $serviceName = explode('.', $itemArray[sizeof($itemArray) - 1])[0];

            $serveces[$serviceName] = $item;
            // $serveces[$serviceName] = str_ireplace($searchRoot, "App", $item);
        }
        return $serveces;
    }

    private static function searchFiles(string $currentRoot, string $fileName, array &$searchResult): void
    {
        if (!is_dir($currentRoot)) {
            if (strrpos($currentRoot, $fileName)) {
                $searchResult[] = $currentRoot;
            }
            return;
        }

        $files = scandir($currentRoot);
        for ($i = 0; $i < count($files); $i++) {
            if (
                is_dir($currentRoot)
                && !(strrpos($currentRoot, '/.') || strrpos($currentRoot, '/..'))
            ) {
                self::searchFiles($currentRoot . "/" . $files[$i],  $fileName, $searchResult);
            }
        }
    }

    public static function getNamespace(string $name): string
    {
        $searchRoot = $_SERVER["DOCUMENT_ROOT"] . "/src";
        $name = str_ireplace($searchRoot, "App", $name);
        $name = str_ireplace('/', '\\', $name);
        return explode('.', $name)[0];
    }

    public static function getService($key, $default = null)
    {
        self::$_storage = self::setStorage();
        if (array_key_exists($key, self::$_storage)) {
            return self::getNamespace(self::$_storage[$key]);
        }
        return $default;
    }
}
