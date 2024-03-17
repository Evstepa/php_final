<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\FilesProvider;

class FilesController
{
    private FilesProvider $filesProvider;

    public function __construct()
    {
        $this->filesProvider = new FilesProvider();
    }

    /**
     * route('/files/list', method='GET')
     *
     * @return array
     */
    public function getFilesList(array $userData): array
    {
        var_dump($userData);
        return [
            'body' => 'FilesController.php->getFilesList',
            'status' => 200,
        ];
    }

    /**
     * route('/files/get/{id}', method='GET')
     *
     * @return array
     */
    public function getFile(array $userData): array
    {
        var_dump($userData);
        return [
            'body' => 'FilesController.php->getFile',
            'status' => 200,
        ];
    }

    /**
     * route('/files/add', method='POST')
     *
     * @return array
     */
    public function addFile(array $userData): array
    {
        var_dump($userData);
        return [
            'body' => 'FilesController.php->addFile',
            'status' => 200,
        ];
    }

    /**
     * route('/files/rename', method='PUT')
     *
     * @return array
     */
    public function renameFile(array $userData): array
    {
        var_dump($userData);
        return [
            'body' => 'FilesController.php->renameFile',
            'status' => 200,
        ];
    }
}
