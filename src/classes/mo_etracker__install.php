<?php
/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 derksen mediaopt GmbH
 */

/**
 * Class for module (de)activation.
 *
 * @author Andre Moelle <andre.moelle@mediaopt.de>
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker
 */
class mo_etracker__install
{

    /**
     * @return string
     */
    protected static function getBootstrapLoaderStatement()
    {
        return 'require_once __DIR__ . \'/mo/etracker/bootstrap.php\'; // This line was automatically generated.';
    }


    /**
     * Add a new column with specified type to a table.
     *
     * This method intercepts exceptions signaling that the given column already exists.
     *
     * @param string $table
     * @param string $column
     * @param string $type
     * @return null
     * @throws Exception
     */
    protected static function addColumn($table, $column, $type)
    {
        try {
            \oxDb::getDb()->execute("ALTER TABLE " . $table . " ADD COLUMN " . $column . " " . $type . ";");
        } catch (Exception $ex) {
            if ($ex->getCode() != 1060) {
                throw $ex;
            }
        }
    }

    /**
     * Deletes every file in the tmp directory.
     *
     * @return null
     */
    protected static function cleanUp()
    {
        foreach (glob(\oxRegistry::getConfig()->getConfigParam('sCompileDir') . '*') as $pathToFile) {
            if (is_file($pathToFile)) {
                unlink($pathToFile);
            }
        }
    }

    /**
     * @return string
     */
    protected static function getFunctionsFile()
    {
        return implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', '..', 'functions.php']);
    }


    /**
     * Returns true iff the module can be installed.
     *
     * @return bool
     */
    public static function isInstallable()
    {
        $issues = [];

        if (file_exists(static::getFunctionsFile()) && !is_writable(static::getFunctionsFile())) {
            $issues[] = 'The functions.php is not writable';
        }
        if (!file_exists(static::getFunctionsFile()) && !is_writable(dirname(static::getFunctionsFile()))) {
            $issues[] = 'The modules directory is not writable.';
        }

        foreach ($issues as $issue) {
            \oxRegistry::get('oxutilsview')->addErrorToDisplay($issue);
        }

        return empty($issues);
    }

    /**
     * Adds the bootstrap loader.
     *
     * @return bool
     */
    public static function onActivate()
    {
        if (!static::isInstallable()) {
            return false;
        }

        static::addBootstrapLoader();
        static::addColumn('oxcategories', 'mo_etracker__name', 'VARCHAR(50) NOT NULL DEFAULT ""');
        \oxNew('oxDbMetaDataHandler')->updateViews();
        static::cleanUp();
        return true;
    }

    /**
     * Adds a line to the functions.php in the modules directory to ensure that the bootstrap is included.
     */
    protected static function addBootstrapLoader()
    {
        $lines = file_exists(static::getFunctionsFile()) ? file(static::getFunctionsFile(),
            FILE_IGNORE_NEW_LINES) : ['<?php'];
        $bootstrapLoader = static::getBootstrapLoaderStatement();

        if (in_array($bootstrapLoader, $lines) !== false) {
            return;
        }

        $lineOfClosingTag = array_search('?>', $lines);
        if ($lineOfClosingTag !== false) {
            $lines = array_slice($lines, 0, $lineOfClosingTag);
        }
        $lines[] = $bootstrapLoader;

        file_put_contents(static::getFunctionsFile(), implode(PHP_EOL, $lines));
    }

    /**
     * Returns true iff the module can be deinstalled.
     *
     * @return bool
     */
    public static function isDeinstallable()
    {
        $issues = [];

        if (file_exists(static::getFunctionsFile()) && !is_writable(static::getFunctionsFile())) {
            $issues[] = 'The functions.php is not writable';
        }

        foreach ($issues as $issue) {
            \oxRegistry::get('oxutilsview')->addErrorToDisplay($issue);
        }

        return empty($issues);
    }

    /**
     * Removes the bootstrap loader.
     */
    public static function onDeactivate()
    {
        if (!static::isDeinstallable()) {
            return false;
        }

        static::removeBootstrapLoader();
        \oxNew('oxDbMetaDataHandler')->updateViews();
        static::cleanUp();
    }

    /**
     * Removes the line in the functions.php in the modules directory that loads the bootstrap, in case this line exists.
     */
    protected static function removeBootstrapLoader()
    {
        if (!file_exists(static::getFunctionsFile())) {
            return;
        }

        $lines = file(static::getFunctionsFile(), FILE_IGNORE_NEW_LINES);
        $lineOfBootstrapLoader = array_search(static::getBootstrapLoaderStatement(), $lines);
        if ($lineOfBootstrapLoader !== false) {
            unset($lines[$lineOfBootstrapLoader]);
        }
        file_put_contents(static::getFunctionsFile(), implode(PHP_EOL, $lines));
    }

}