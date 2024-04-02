<?php

namespace App\Entity;

use DateTime;

final class File
{
    private int $id;

    private string $name = '';

    private int $size = 0;

    private array $owners = [];

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

    public function getId(): int
    {
        return $this->id;
    }

    private function setPath(string $path): self
    {
        $this->path = explode('/', $path);
        return $this;
    }

    public function getPath(): string
    {
        return implode('/', $this->path);
    }

    public function getShortPath(): string
    {
        return implode('/', array_slice($this->path, 0, -1));
    }

    public function setOwners(string $owners): self
    {
        $this->owners = (array) $owners;
        return $this;
    }

    public function getOwners(): array
    {
        $owners = $this->owners;
        // $owners[] = ; определить владельца = пользователя

        return array_unique($owners);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(): self
    {
        $this->name = $this->path[count($this->path) - 1];
        return $this;
    }

    public function getFolder(): string
    {
        return $this->folder;
    }

    public function setFolder(): self
    {
        $folderKey = array_search(UPLOAD_USER_ROOT, $this->path) + 1;
        $this->folder .= implode('/', array_slice($this->path, $folderKey + 1, -1));

        return $this;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(): self
    {
        $this->size = filesize($this->getPath());

        return $this;
    }

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

    public function getLastAccessAt(): string
    {
        return $this->lastAccessAt->format('F d Y H:i:s');
    }

    public function isFile(): bool
    {
        return is_file($this->name);
    }

    public function exportData(): array
    {
        return [
            // 'id' => $this->getId(),
            'name' => $this->getName(),
            'size' => $this->getSize(),
            'folder' => $this->getFolder(),
            'lastAccessAt' => $this->getLastAccessAt(),
        ];
    }
}
