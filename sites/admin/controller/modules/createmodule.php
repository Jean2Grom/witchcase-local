<?php

$messages = [];

if( filter_has_var(INPUT_POST, "createButton") )
{   $action = "create"; }

else
{   $action = "displayForm"; }

if( strcmp($action, "create") == 0 )
{
    $name = filter_input(INPUT_POST, "name");
    $site = filter_input(INPUT_POST, "site");
    
    $cleanName = Localisation::cleanupString($name);
    
    if( !$name )
    {
        $messages[] = "Vous devez saisir un nom valide";
        $action     = "displayForm";
    }
    elseif( !$configuration->addVariable( $site, 'modules', $cleanName, true )  )
    {
        $messages[] = "L'écriture a échoué";
        $action     = "displayForm";
    }
    else
    {
        if( strcmp($site, "global") == 0 )
        {   $site = "default";   }
        
        $siteDir = "sites/".$site;
        if( !is_dir($siteDir) )
        {
            mkdir( $siteDir, 0705 );
            $controllerDir  = $siteDir."/controller";
            $designDir      = $siteDir."/design";
            mkdir( $controllerDir, 0705 );
            mkdir( $designDir, 0705 );
            $controllerDir .= "/modules";
            $designDir .= "/modules";
            mkdir( $controllerDir, 0705 );
            mkdir( $designDir, 0705 );
        }
        else 
        {
            $controllerDir  = $siteDir."/controller/modules";
            $designDir      = $siteDir."/design/modules";
        }

        $moduleControllerFile   = $controllerDir."/".$cleanName.".php";
        $moduleDesignFile       = $designDir."/".$cleanName.".php";

        if( !file_exists($moduleControllerFile) )
        {
            $fileContent = "<?php\n\ninclude \$module->getDesignFile();";
            
            $fp = fopen($moduleControllerFile, "w");
            fwrite($fp, $fileContent);
            fclose($fp);
        }
        
        if( !file_exists($moduleDesignFile) )
        {
            $fileContent = "<?php\n\n?>\n\n<h1>MODULE  ".$cleanName."</h1>";
            
            $fp = fopen($moduleDesignFile, "w");
            fwrite($fp, $fileContent);
            fclose($fp);
        }
        
        $returnHref = "http://".$localisation->siteAccess;
        
        include $module->getDesignFile('modules/createmodule/success.php');
    }
}

if( strcmp($action, "displayForm") == 0 )
{
    $sites = [ 'global' ];
    
    foreach( $configuration->administratedSites($localisation->site) as $adminisratedSite )
    {   $sites[] = $adminisratedSite;   }

    $cancelHref = "http://".$localisation->siteAccess;

    include $module->getDesignFile();
}