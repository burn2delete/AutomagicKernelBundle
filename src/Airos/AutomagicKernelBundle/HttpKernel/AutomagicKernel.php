<?php

namespace Airos\AutomagicKernelBundle\HttpKernel;

use Airos\AutomagicKernelBundle\Configuration\BundleConfiguration;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

abstract class AutomagicKernel extends Kernel
{
    public function registerBundles()
    {
        
        $autoloadFile = 'autoload.yml';
        
        $bundles = array();
        $parsedAutoloadFiles = array();
        
        $finder = new Finder();
        
        $dirs = array();
        $dirs[] = $this->getRootDir().'/config';
        $dirs[] = $this->getRootDir().'/../vendor';
        $dirs[] = $this->getRootDir().'/../src';
        
        $finder->in($dirs)->files()->name($autoloadFile);
        
        foreach ($finder as $file)
        {
            
            if ($file->getContents() != null) {
                
                $parsedAutoloadFiles[] = Yaml::parse($file->getPathname());
                
            }
            
        }
        
        $processor = new Processor();
        $configuration = new BundleConfiguration;
        
        $parsedAutoloadFiles = (count($parsedAutoloadFiles) == 1) ? $parsedAutoloadFiles[0] : $parsedAutoloadFiles;
        
        $processedConfiguration = $processor->processConfiguration($configuration, $parsedAutoloadFiles);
        
        foreach ($processedConfiguration as $autoloadBundles => $bundleArray)
        {
            
            foreach ($bundleArray as $bundleName => $bundleInfo)
            {
                
                if (in_array($this->getEnvironment(), $bundleInfo['env']) || in_array('all', $bundleInfo['env']))
                {
                    
                    $bundles[] = ($bundleInfo['kernel'] == true) ? new $bundleInfo['fqcn']($this) : new $bundleInfo['fqcn']() ;
                    
                }
                
            }
            
        }
        
        return $bundles;
    }

}