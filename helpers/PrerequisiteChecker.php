<?php

class ERequirementNotMet extends Exception {
}

class PrerequisiteChecker {
    
    public function checkRequirements() {
        self::checkForConfigFile();
        self::checkForConfigDistFile();
        self::checkForComposer();
        self::checkForYarn();
    }
    
    
    private function checkForConfigFile() {
        if (!file_exists(__DIR__ . "/../data/config.php"))
            throw new ERequirementNotMet("/data/config.php not found. Have you copied config-dist.php to the data directory and renamed it to config.php?");
    }

    private function checkForConfigDistFile() {
        if (!file_exists(__DIR__ . "/../config-dist.php"))
            throw new ERequirementNotMet("config-dist.php not found. Please do not remove this file.");
    }

    private function checkForComposer() {
        if (!file_exists(__DIR__ . "/../vendor/autoload.php"))
            throw new ERequirementNotMet("/vendor/autoload.php not found. Have you run Composer?");
    }

    private function checkForYarn() {
        if (!file_exists(__DIR__ . "/../public/node_modules"))
            throw new ERequirementNotMet("/public/node_modules not found. Have you run Yarn?");
    }
}


?>