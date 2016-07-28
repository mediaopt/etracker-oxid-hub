<?php

class CustomBundleConfig extends BundleConfig
{

    public function getRepositoryRoot()
    {
        return $this->getBaseDir();
    }

}
