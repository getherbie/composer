<?php

/**
 * This file is part of Herbie.
 *
 * (c) Thomas Breuss <www.tebe.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace herbie\composer;

use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Script\CommandEvent;
use Composer\Util\Filesystem;

class Installer extends LibraryInstaller
{

    const EXTRA_WRITABLE = 'writable';

    const EXTRA_EXECUTABLE = 'executable';

    /**
     * Sets the correct permission for the files and directories listed in the extra section.
     * @param CommandEvent $event
     */
    public static function setPermission($event)
    {
        $options = array_merge([
            self::EXTRA_WRITABLE => [],
            self::EXTRA_EXECUTABLE => [],
            ], $event->getComposer()->getPackage()->getExtra());

        foreach ((array) $options[self::EXTRA_WRITABLE] as $path) {
            echo "Setting writable: $path ...";
            if (is_dir($path)) {
                chmod($path, 0777);
                echo "done\n";
            } else {
                echo "The directory was not found: " . getcwd() . DIRECTORY_SEPARATOR . $path;
                return;
            }
        }

        foreach ((array) $options[self::EXTRA_EXECUTABLE] as $path) {
            echo "Setting executable: $path ...";
            if (is_file($path)) {
                chmod($path, 0755);
                echo "done\n";
            } else {
                echo "\n\tThe file was not found: " . getcwd() . DIRECTORY_SEPARATOR . $path . "\n";
                return;
            }
        }
    }
}
