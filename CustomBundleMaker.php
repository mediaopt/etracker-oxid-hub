<?php

include_once __DIR__ . DIRECTORY_SEPARATOR . 'CustomBundleConfig.php';

class CustomBundleMaker extends BundleMaker
{

    public function __construct($scmType, $baseDir, $climate = false)
    {
        parent::__construct($scmType, $baseDir, $climate);
        $this->config = new CustomBundleConfig($baseDir, $scmType, $climate);
    }

    protected function exportSrc()
    {
        $base = $this->config->getBaseDir();
        $temp = $this->config->getTemporaryFolder();

        `mkdir -p $temp`;
        `cp $base/docs/*.* $temp/`;

        $module = $temp . DIRECTORY_SEPARATOR . $this->config->getPathToModule();
        `mkdir -p $module`;
        $this->writeVendorMetadata();
        `cp -R $base/src/* $module/`;
        `rm -fR $module/tests/reports`;

        `find $temp -name "*~" -type f -delete`;
        `find $temp -name ".gitkeep" -type f -delete`;
    }

    protected function writeVendorMetadata()
    {
        $segments = array($this->config->getTemporaryFolder(), $this->config->getPathToModule(), '..', 'vendormetadata.php');
        $path = implode(DIRECTORY_SEPARATOR, $segments);
        $vendorMetadata = implode("\n", array('<?php', '$sVendorMetadataVersion = \'1.0\';'));
        file_put_contents($path, $vendorMetadata);
    }

}
