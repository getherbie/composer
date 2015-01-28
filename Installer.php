<?php

/**
 * This file is part of Herbie.
 *
 * (c) Thomas Breuss <www.tebe.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Herbie\Composer;

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
     * {@inheritDoc}
     */
    public function getInstallPath(PackageInterface $package)
    {
        $prefix = substr($package->getPrettyName(), 0, 17);
        if ('getherbie/plugin-' !== $prefix) {
            throw new \InvalidArgumentException(
                'Unable to install herbie plugin. The package name should always start with "getherbie/plugin-"'
            );
        }

        $basePath = 'site/plugins/'.substr($package->getPrettyName(), 17);

        return $basePath;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        return 'herbie-plugin' === $packageType;
    }

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
