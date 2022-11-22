<?php

/**
 * Disk Status Class
 *
 * http://pmav.eu/stuff/php-disk-status/
 *
 */
class DiskStatus
{

    private $diskPath;

    function __construct($diskPath)
    {
        $this->diskPath = $diskPath;
    }

    public function totalSpace()
    {
        $diskTotalSpace = disk_total_space($this->diskPath);

        if ($diskTotalSpace === FALSE) {
            throw new Exception('totalSpace(): Invalid disk path.');
        }

        return $this->addUnits($diskTotalSpace);
    }

    public function freeSpace()
    {
        $diskFreeSpace = disk_free_space($this->diskPath);

        if ($diskFreeSpace === FALSE) {
            throw new Exception('freeSpace(): Invalid disk path.');
        }

        return $this->addUnits($diskFreeSpace);
    }

    public function usedSpace($precision = 1)
    {
        try {
            $diskUsedSpace = disk_total_space($this->diskPath) - disk_free_space($this->diskPath);
            return $this->addUnits($diskUsedSpace);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function usedSpacePercentage($precision = 1)
    {
        try {
            return round((100 - ($this->freeSpace() / $this->totalSpace()) * 100), $precision);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getDiskPath()
    {
        return $this->diskPath;
    }

    private function addUnits($bytes)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 1) . ' ' . $units[$i];
    }

}

?>