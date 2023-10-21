<?php

namespace Qtran2015\StorageDir;

use Qtran2015\StorageDir\Exceptions\MkdirPermissionException;

class DirService {
    protected string $storage;
    protected string $dir;
    protected int $permissions;

    public function __construct()
    {
        $this->setStorage(storage_path())->setPermissions(0755);
    }

    /**
     * @param string $dir
     */
    public function setDir(string $dir = ''): static
    {
        $this->dir = empty(trim($dir)) ? '' : trim($dir, '/').'/';
        return $this;
    }

    public function fullAccess(): static
    {
        $this->setPermissions(0777);
        return $this;
    }

    public function execute(): array
    {
        $newDir = $this->getStorage().$this->getDir();
        return ! (file_exists($newDir))
            ? $this->makeDir()
            : [
                'status' => 'exists',
                'abs_path' => $newDir,
                'rel_path' => $this->getDir(),
            ];
    }

    protected function makeDir(): array
    {
        $absDir = $this->getStorage();
        try {
            foreach (explode('/', $this->getDir()) as $dir) {
                $dir = trim($dir);
                if (empty($dir)) continue;
                $absDir .= $dir.'/';
                if (file_exists($absDir)) continue;
                if (! mkdir($absDir, $this->getPermissions(), true)) throw new MkdirPermissionException('Permission denied: failed to create directory '.$absDir);
                chmod($absDir, $this->getPermissions());
            }
        }
        catch (MkdirPermissionException|\Exception $e) {
            return [
                'status' => 'failed',
                'message' => 'Permission denied: failed to create directory '.$absDir,
                'abs_path' => $this->getStorage(),
                'rel_path' => '',
            ];
        }

        return [
            'status' => 'created',
            'abs_path' => $absDir,
            'rel_path' => $this->getDir(),
        ];
    }

    /**
     * @return string
     */
    protected function getStorage(): string
    {
        return $this->storage;
    }

    /**
     * @param string $storage
     */
    protected function setStorage(string $storage): static
    {
        $this->storage = rtrim($storage, '/').'/';
        return $this;
    }

    /**
     * @return string
     */
    protected function getDir(): string
    {
        return $this->dir;
    }

    /**
     * @return int
     */
    protected function getPermissions(): int
    {
        return $this->permissions;
    }

    /**
     * @param int $permissions
     */
    protected function setPermissions(int $permissions): static
    {
        $this->permissions = $permissions;
        return $this;
    }

}
