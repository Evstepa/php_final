<?php

declare(strict_types=1);

namespace App\Core;

class App
{
    public $_storage = [];

    public function __construct()
    {
        $this->_storage = $this->setStorage();
    }

    private function setStorage(): array
    {
        $searchRoot = $_SERVER["DOCUMENT_ROOT"] . "/src";
        $fileName = '.php';
        $searchResult = [];
        $this->searchFiles($searchRoot, $fileName, $searchResult);
        $searchResult = array_filter($searchResult, "filesize");

        $serveces = [];
        foreach ($searchResult as $key => $item) {
            $itemArray = explode('/', $item);
            $serviceName = explode('.', $itemArray[sizeof($itemArray) - 1])[0];

            $serveces[$serviceName] = $item;
            // $serveces[$serviceName] = str_ireplace($searchRoot, "", $item);
        }

        return $serveces;
    }

    private function searchFiles(string $currentRoot, string $fileName, array &$searchResult): void
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
                $this->searchFiles($currentRoot . "/" . $files[$i],  $fileName, $searchResult);
            }
        }
    }

    public function getStorage(): array
    {
        return $this->_storage;
    }

    public function getService($key, $default = null): ?string
    {
        $currentKey = explode('::', $key);
        if (array_key_exists($currentKey[0], $this->_storage)) {
            require_once($this->_storage[$currentKey[0]]);
            return $currentKey[1];
        }
        return $default;
    }
}
