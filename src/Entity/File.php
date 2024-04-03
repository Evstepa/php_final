<?php

namespace App\Entity;

use DateTime;

final class File
{
    private int $id;

    private string $name = '';

    private int $size = 0;

    private array $path = [];

    private string $folder = '/';

    private DateTime $lastAccessAt;

    public function __construct(array $pathData)
    {
        $this->id = $pathData['id'];
        $this->setPath($pathData['path']);
        $this->setName();
        $this->setFolder();
        $this->setSize();
        $this->setLastAccessAt();
    }

    /**
     * Получить id файла в БД
     *
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Установить путь поиска файла в файловой системе в виде массива
     *
     * @param string $path
     * @return self
     */
    private function setPath(string $path): self
    {
        $this->path = explode('/', $path);
        return $this;
    }

    /**
     * вернуть полный путь поиска файла в виде строки
     *
     * @return string
     */
    public function getPath(): string
    {
        return implode('/', $this->path);
    }

    /**
     * Вернуть путь поиска файла в виде строки без имени файла
     *
     * @return string
     */
    public function getShortPath(): string
    {
        return implode('/', array_slice($this->path, 0, -1));
    }

    /**
     * Установить имя файла
     *
     * @return self
     */
    public function setName(): self
    {
        $this->name = $this->path[count($this->path) - 1];
        return $this;
    }

    /**
     * Получить имя файла
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Установить имя папки последнего уровня
     *
     * @return self
     */
    public function setFolder(): self
    {
        $folderKey = array_search(UPLOAD_USER_ROOT, $this->path) + 1;
        $this->folder .= implode('/', array_slice($this->path, $folderKey + 1, -1));

        return $this;
    }

    /**
     * Получить имя папки последнего уровня
     *
     * @return string
     */
    public function getFolder(): string
    {
        return $this->folder;
    }

    /**
     * Установить размер файла
     *
     * @return self
     */
    public function setSize(): self
    {
        $this->size = filesize($this->getPath());

        return $this;
    }

    /**
     * Получить размер файла
     *
     * @return integer
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Установить время последнего обращения к файлу
     *
     * @return self
     */
    public function setLastAccessAt(): self
    {
        if (!fileatime($this->getPath())) {
            $this->lastAccessAt = new DateTime();
        } else {
            $this->lastAccessAt = new DateTime(
                date("F-d-Y H:i:s", fileatime($this->getPath()))
            );
        }

        return $this;
    }

    /**
     * Вернуть время последнего обращения к файлу
     *
     * @return string
     */
    public function getLastAccessAt(): string
    {
        return $this->lastAccessAt->format('F d Y H:i:s');
    }

    /**
     * Проверить, является ли одъект файлом
     *
     * @return boolean
     */
    public function isFile(): bool
    {
        return is_file($this->name);
    }

    /**
     * Выгрузка данных из объекта в массив
     *
     * @return array
     */
    public function exportData(): array
    {
        return [
            'name' => $this->getName(),
            'size' => $this->getSize(),
            'folder' => $this->getFolder(),
            'lastAccessAt' => $this->getLastAccessAt(),
        ];
    }
}
