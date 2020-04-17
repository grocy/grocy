<?php

class ERequirementNotMet extends Exception { }

const REQUIRED_PHP_EXTENSIONS = array('fileinfo', 'pdo_sqlite', 'gd');

class PrerequisiteChecker
{
    public function checkRequirements()
    {
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
}
