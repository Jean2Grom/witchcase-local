<?php

use WC\Witch;
use WC\TargetStructure;

$messages = [];

//$this->debug->dump($_POST, 'POST');

if( filter_has_var(INPUT_POST, "createButton") )
{
    $valid          = true;
    $siteNamePost   = filter_input(INPUT_POST, "name");
    $siteName       = Witch::cleanupString($siteNamePost);
    
    if( !$siteName )
    {
        $valid      = false;
        $messages[] = "Vous devez entrer un nom valide pour le site";
    }
    
    $siteAccessPost = filter_input(INPUT_POST, "siteAccess");
    
    $siteAccess = [];
    foreach( explode(PHP_EOL, $siteAccessPost) as $siteAccessPost_item )
    {
        $siteAccessRaw = strtolower( trim($siteAccessPost_item) );
        if( empty($siteAccessRaw) )
        {   continue;   }
        
        if( !filter_var("https://".$siteAccessRaw, FILTER_VALIDATE_URL) 
            || count( explode('/', $siteAccessRaw) ) > 2
        ){
            $valid      = false;
            $messages[] = "L'accès : ".$siteAccessRaw." n'est pas valide";
        }
        else
        {   $siteAccess[] = $siteAccessRaw; }
    }
    
    if( !$siteAccessPost || count($siteAccess) == 0 )
    {
        $valid      = false;
        $messages[] = "Vous devez entrer au moins un accès au site";
    }
    
    $homeNamePost   = filter_input(INPUT_POST, "nameHome");
    if( !$homeNamePost )
    {
        $valid      = false;
        $messages[] = "Vous devez entrer un nom valide pour votre page d'accueil.";
    }
    
    if( $valid )
    {
        if( !$configuration->addVariable( 'global', 'sites', $siteName, true )  
            || !$iniFile = $configuration->addVariable( $siteName, 'access', $siteAccess )  
        ){
            $valid      = false;
            $messages[] = "L'écriture du site a échoué";
        }
        
        $siteHeritages = filter_input( INPUT_POST, "heritage" );
        if( $valid && $siteHeritages 
            && !$configuration->addVariable( $siteName, 'siteHeritages', $siteHeritages, true ) )
        {
            $valid      = false;
            $messages[] = "L'écriture de l'héritage a échoué";
        }
        
        $adminForSite = filter_input(   INPUT_POST, 
                                        "adminForSite",
                                        FILTER_DEFAULT, 
                                        FILTER_REQUIRE_ARRAY 
                        );
        if( $valid && $adminForSite 
            && !$configuration->addVariable( $siteName, 'adminForSite', $adminForSite ) )
        {
            $valid      = false;
            $messages[] = "L'écriture a échoué";
        }
        
        if( $valid )
        {
            $siteDir = "sites/".$siteName;
            if( !is_dir($siteDir) && mkdir( $siteDir, 0705 ) )
            {
                $controllerDir  = $siteDir."/controller";
                $designDir      = $siteDir."/design";
                if( mkdir( $controllerDir, 0705 ) && mkdir( $designDir, 0705 ) )
                {
                    foreach( ['modules', 'attributes', 'contexts'] as $folder )
                    {
                        mkdir( $controllerDir."/".$folder, 0705 );
                        mkdir( $designDir."/".$folder, 0705 );
                    }
                }
                
                mkdir( $designDir."/css", 0705 );
                mkdir( $designDir."/images", 0705 );
                mkdir( $designDir."/js", 0705 );
            }
            
            $description    =   filter_input(   INPUT_POST, "description" );
            $modulePost     =   filter_input(   INPUT_POST, "moduleHome" );
            $structure      =   filter_input(   INPUT_POST, "structureHome" );
            
            $parentsID = [ $this->configuration->read('global', 'rootID') ];
            
            if( !in_array($modulePost, $configuration->getModules($localisation->site)) )
            {   $modulePost = 'view';   }
            
            if( !empty($structure) )
            {
                $draft      =   new Draft( strtolower($structure) );
                $result     =   $draft->create( $parentsID, 
                                                $modulePost,
                                                $homeNamePost, 
                                                $description, 
                                                '/', 
                                                $siteName
                                );
                
                $redirectionURI = "http://".$localisation->siteAccess."/edit?localisation=".$result;
            }
            else
            {
                
//                $result =   Location::create(   $parentsID, 
//                                                $homeNamePost, 
//                                                $modulePost, 
//                                                '', 
//                                                NULL, 
//                                                $description, 
//                                                '/'
//                            );
                
                //$destination = new Localisation($result);
                
                $redirectionURI = "http://".$localisation->siteAccess.$destination->url;
            }
            
            if( !$result )
            {
                $valid      = false;
                $messages[] = "Could not create draft, please try again";
            }
            else
            {
                header('Location: '.$redirectionURI );
                exit();
            }
        }
        
        if( !$valid && $iniFile )
        {
            $configuration->rollback($iniFile);
        }
    }
    
}


$sites = $this->configuration->getAdministratedSites($this->localisation->site);

$modulesVar     = $this->configuration->getModulesVariables( $this->localisation->site );
$defaultModule  = $modulesVar["create"]["defaultModule"];

$modulesDisplay = [];
foreach( $modulesVar as $moduleName => $variables ){
    $modulesDisplay[] = [
        'name'      => $moduleName,
        'default'   => ( strcmp($defaultModule, $moduleName) == 0 )
    ];
}

$structureList = TargetStructure::listStructures( $this->wc );

$structures = [];
foreach( $structureList as $structureItem )
{
    $structures[]   =   [   "label" => ucfirst($structureItem['name']),
                            "value" => $structureItem['name']
                        ];
}

$cancelHref = "http://".$localisation->siteAccess;


include $module->getDesignFile();