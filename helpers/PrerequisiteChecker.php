<?php

class ERequirementNotMet extends Exception { }

const REQUIRED_PHP_EXTENSIONS        = array('fileinfo', 'pdo_sqlite', 'gd');
const REQUIRED_SQLITE_VERSION_INT    = "3008003"; //3.8.3 - this value will be checked
const REQUIRED_SQLITE_VERSION_STRING = "3.8.3";   //This value is just for error output, no check is done

class PrerequisiteChecker
{
    public function checkRequirements()
    {
        self::checkForSqliteVersion();
        self::checkForConfigFile();
        self::checkForConfigDistFile();
        self::checkForComposer();
        self::checkForPhpExtensions();
    }
    
    
    private function checkForConfigFile()
    {
        if (!file_exists(GROCY_DATAPATH . '/config.php'))
        {
            throw new ERequirementNotMet('config.php in data directory (' . GROCY_DATAPATH . ') not found. Have you copied config-dist.php to the data directory and renamed it to config.php?');
        }
    }

    private function checkForConfigDistFile()
    {
        if (!file_exists(__DIR__ . '/../config-dist.php'))
        {
            throw new ERequirementNotMet('config-dist.php not found. Please do not remove this file.');
        }
    }

    private function checkForComposer()
    {
        if (!file_exists(__DIR__ . '/../vendor/autoload.php'))
        {
            throw new ERequirementNotMet('/vendor/autoload.php not found. Have you run Composer?');
        }
    }

    private function checkForPhpExtensions()
    {
        $loadedExtensions = get_loaded_extensions();
        foreach (REQUIRED_PHP_EXTENSIONS as $extension)
        {
            if (!in_array($extension, $loadedExtensions))
            {
                throw new ERequirementNotMet("PHP module '{$extension}' not installed, but required.");
            }
        }
    }


    private function checkForSqliteVersion()
    {
        $sqliteVersion = SQLite3::version()["versionNumber"];
        if ($sqliteVersion < REQUIRED_SQLITE_VERSION_INT)
        {
            throw new ERequirementNotMet('SQLite ' . REQUIRED_SQLITE_VERSION_STRING . ' is required, however you are running ' . SQLite3::version()["versionString"]);
        }
    }
}
