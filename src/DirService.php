<?php

namespace Qtran2015\StorageDir;

use Qtran2015\StorageDir\Exceptions\MkdirPermissionException;

class DirService {
    const EXISTS_STATUS = 'exists';
    const CREATED_STATUS = 'created';
    const FAILED_STATUS = 'failed';
    const STANDARD_PERMISSION = 0755;
    const FULL_ACCESS_PERMISSION = 0777;

    protected string $storage;
    protected string $dir;
    protected int $permissions;
    protected array $dirAccessFlag;

    public function __construct()
    {
        $this->setStorage(storage_path())
            ->setPermissions(self::STANDARD_PERMISSION)
            ->initDirAccessFlag()
        ;
    }

    /**
     * @param string $dir
     * @return DirService
     */
    public function setDir(string $dir = ''): DirService
    {
        $this->dir = empty(trim($dir)) ? '' : trim($dir, '/').'/';
        return $this;
    }

    /**
     * @return DirService
     */
    public function fullAccess(): DirService
    {
        $this->setPermissions(self::FULL_ACCESS_PERMISSION);
        return $this;
    }

    /**
     * @param string $dir
     * @return DirService
     */
    public function giveFullAccessDir(string $dir): DirService
    {
        return $this->addToDirAccessFlag($dir, self::FULL_ACCESS_PERMISSION);
    }

    /**
     * @param string $dir
     * @return DirService
     */
    public function removeFullAccessDir(string $dir): DirService
    {
        return $this->addToDirAccessFlag($dir, self::STANDARD_PERMISSION);
    }

    public function execute(): array
    {
        $newDir = $this->getStorage().$this->getDir();
        return file_exists($newDir) ? $this->existDir($newDir) : $this->makeDir();
    }

    protected function processDirAccessFlag(): void
    {
        if (empty($this->dirAccessFlag)) return;

        $absDir = $this->getStorage();
        foreach (explode('/', $this->getDir()) as $dir) {
            $dir = trim($dir);
            if (empty($dir)) continue;
            $absDir .= $dir.'/';
            if (file_exists($absDir) && isset($this->dirAccessFlag[$dir])) {
                chmod($absDir, $this->dirAccessFlag[$dir]);
            }
        }
    }

    protected function existDir($newDir): array
    {
        $this->processDirAccessFlag();

        return [
            'status' => self::EXISTS_STATUS,
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
                'status' => self::FAILED_STATUS,
                'message' => 'Permission denied: failed to create directory '.$absDir,
                'abs_path' => $this->getStorage(),
                'rel_path' => '',
            ];
        }

        $this->processDirAccessFlag();

        return [
            'status' => self::CREATED_STATUS,
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
     * @return DirService
     */
    protected function setStorage(string $storage): DirService
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
     * @return DirService
     */
    protected function setPermissions(int $permissions): DirService
    {
        $this->permissions = $permissions;
        return $this;
    }

    /**
     * @return DirService
     */
    protected function initDirAccessFlag(): DirService
    {
        $this->dirAccessFlag = [];
        return $this;
    }

    /**
     * @param string $dir
     * @param $permission
     * @return DirService
     */
    protected function addToDirAccessFlag(string $dir, $permission): DirService
    {
        $this->dirAccessFlag[trim($dir)] = $permission;
        return $this;
    }
}
