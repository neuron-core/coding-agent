<?php

declare(strict_types=1);

namespace NeuronCore\Maestro\Extension;

use NeuronCore\Maestro\Commands\DiscoverCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Throwable;

use function file_exists;
use function filemtime;

/**
 * Manages the extension manifest lifecycle.
 *
 * Automatically regenerates the manifest when it's missing or stale
 * (i.e., when composer.lock or installed.json is newer than the manifest).
 */
class ManifestManager
{
    protected const MANIFEST_PATH = '.maestro/manifest.php';
    protected const COMPOSER_LOCK_PATH = 'composer.lock';
    protected const INSTALLED_JSON_PATH = 'vendor/composer/installed.json';

    /**
     * Ensure the manifest is up-to-date, regenerating if necessary.
     *
     * @return bool True if manifest exists and is valid, false otherwise
     */
    public function ensureManifestExists(): bool
    {
        if ($this->manifestIsUpToDate()) {
            return true;
        }

        return $this->regenerateManifest();
    }

    /**
     * Check if the manifest exists and is newer than composer files.
     */
    protected function manifestIsUpToDate(): bool
    {
        if (!file_exists(self::MANIFEST_PATH)) {
            return false;
        }

        $manifestTime = filemtime(self::MANIFEST_PATH);

        if ($manifestTime === false) {
            return false;
        }

        // Check composer.lock
        if (file_exists(self::COMPOSER_LOCK_PATH)) {
            $lockTime = filemtime(self::COMPOSER_LOCK_PATH);
            if ($lockTime !== false && $lockTime > $manifestTime) {
                return false;
            }
        }

        // Check installed.json
        if (file_exists(self::INSTALLED_JSON_PATH)) {
            $installedTime = filemtime(self::INSTALLED_JSON_PATH);
            if ($installedTime !== false && $installedTime > $manifestTime) {
                return false;
            }
        }

        return true;
    }

    /**
     * Regenerate the manifest by running the discover command.
     */
    protected function regenerateManifest(): bool
    {
        $command = new DiscoverCommand();
        $input = new ArrayInput([]);
        $output = new NullOutput();

        try {
            $result = $command->run($input, $output);
            return $result === 0;
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * Get the path to the manifest file.
     */
    public function getManifestPath(): string
    {
        return self::MANIFEST_PATH;
    }
}
