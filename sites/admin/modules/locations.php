<?php

use WC\Location;
use WC\Localisation;

$messages = [];
if( filter_has_var(INPUT_POST, "publishLocationsEdit")  ){
    $action = "publishLocationsEdit";
}
elseif( filter_has_var(INPUT_POST, "addLocation")  ){
    $action = "addLocation";
}
elseif( filter_has_var(INPUT_POST, "deleteLocation")  ){
    $action = "deleteLocation";
}
elseif( filter_has_var(INPUT_POST, "createButton")  ){
    $action = "create";
}
else {
    $action = "display";
}

$baseLocalisationId = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
$baseLocalisation   = new Localisation( $this, $baseLocalisationId );

if( strcmp($action, "publishLocationsEdit") == 0 
    || strcmp($action, "addLocation") == 0
    || strcmp($action, "deleteLocation") == 0
) {
    $uriEditParts = filter_input(   INPUT_POST, 
                                    "uriEditPart", 
                                    FILTER_DEFAULT, 
                                    FILTER_REQUIRE_ARRAY
                    );
    $names      =  filter_input(   INPUT_POST, 
                                    "name", 
                                    FILTER_DEFAULT, 
                                    FILTER_REQUIRE_ARRAY
                    );
    $descriptions = filter_input(   INPUT_POST, 
                                    "description", 
                                    FILTER_DEFAULT, 
                                    FILTER_REQUIRE_ARRAY
                    );
    
    foreach( $names as $localisationID => $name )
    {
        if( $baseLocalisationId ==  $localisationID ){
            $editLocalisation = $baseLocalisation;
        }
        else {
            $editLocalisation = new Localisation( $this, $localisationID );
        }
        
        if( $uriEditParts )
        {
            $buffer     = explode('/', $editLocalisation->url);
            $lastIndice = count($buffer) - 1;
            unset( $buffer[$lastIndice] );
            
            $newUrl =   implode('/', $buffer).'/';
            $newUrl .=  Localisation::cleanupString($uriEditParts[$localisationID]);
        }
        else {
            $newUrl = false;
        }
        
        if( !$editLocalisation->edit( $newUrl, $name, $descriptions[$localisationID] ) )
        {
            $message    = "Edition of localisation of ID :".$localisationID." Failed";
            $messages[] = $message;
            $log->error($message);
        }
        else
        {
            $message    = "Edition of localisation of ID :".$localisationID." Succed";
            $messages[] = $message;
        }
    }
    
    if( strcmp($action, "publishLocationsEdit") == 0 ){
        $action = "display";
    }
}

if( strcmp($action, "create") == 0 )
{
    $parentID   = filter_input(INPUT_POST, "localisationID", FILTER_VALIDATE_INT);
    $name       = filter_input(INPUT_POST, "name");
    $customUrl  = filter_input(INPUT_POST, "customUrl");
    $description= filter_input(INPUT_POST, "description");
    
    $location = Location::getFromLocalisationID( $this, $parentID );
    
    $location->addChild($name, 
                        $baseLocalisation->module, 
                        $baseLocalisation->craft_table, 
                        $baseLocalisation->craft_fk, 
                        $description, 
                        $customUrl
    );
    
    $action = "display";
}

if( strcmp($action, "addLocation") == 0 )
{
    $allLocalisations = Location::getAll( $this );
    
    foreach( $allLocalisations as $key => $allLocalisations_item )
    {
        foreach( $allLocalisations_item as $label => $value ){
            if( strcmp("level_", substr($label, 0, 6))==0 ){
                if( !is_null($value) ){
                    $level = (int) substr($label, 6);
                }
            }
        }
        
        $spacing = "";
        for( $i=2; $i<=$level; $i++ ){
            $spacing .= "&nbsp;&nbsp;&nbsp;";
        }
        
        if( $level > 1 ){
            $spacing .= "-&nbsp;";
        }
        
        $allLocalisations[$key]['spacing'] = $spacing;
    }
    
    $cancelHref =   "https://".$this->localisation->siteAccess.$this->localisation->url;
    $cancelHref .=  "?id=".$baseLocalisationId;
    
    include $this->module->getDesignFile('modules/locations/create.php');
}

if( strcmp($action, "deleteLocation") == 0 )
{
    $deleteLocationPost =   filter_input(   INPUT_POST, 
                                            "deleteLocation", 
                                            FILTER_DEFAULT, 
                                            FILTER_REQUIRE_ARRAY
                            );
    
    foreach( $deleteLocationPost as $key => $value ){
        $locationId = $key;
    }
    
    $redirectHref = false;
    if( strcmp($locationId, $baseLocalisation->location_id) == 0 ){
        foreach( $baseLocalisation->sameCraft() as $sameCraftLocalisations ){
            if( strcmp($sameCraftLocalisations['location_id'], $locationId) != 0 
                && strcmp($sameCraftLocalisations['site'], $localisation->site) == 0 
            ){
                $redirectHref =   "https://".$this->localisation->siteAccess.$this->localisation->url;
                $redirectHref .=  "?id=".$sameCraftLocalisations['id'];
                
                break;
            }
        }
    }
    
    $location = Location::getFromLocationID( $this, $locationId);
    
    $deletedArray = $location->delete();
    
    if( $redirectHref )
    {
        header( 'Location: '.$redirectHref );
        exit();
    }
    
    $action = "display";
}

if( strcmp($action, "display") == 0 )
{
    $locations =    $baseLocalisation->sameCraftLocations();
    
    foreach( $locations as $linkId => $localisationsArray ){
        foreach( $localisationsArray as $key => $localisationsArrayItem )
        {
            if( strcmp($localisationsArrayItem['site'], $this->localisation->site) == 0 ){
                $siteaccess = "https://".$this->localisation->siteAccess;
            }
            else {
                $siteaccess = "https://".$this->configuration->read($localisationsArrayItem['site'], 'access')[0];
                
            }
            
            $buffer = explode('/', $localisationsArrayItem['url']);
            $lastIndice = count($buffer) - 1;
            
            $locations[$linkId][$key]['uriEditPart'] = $buffer[$lastIndice];
            unset( $buffer[$lastIndice] );
            
            $locations[$linkId][$key]['uriBasePart'] = $siteaccess.implode('/', $buffer).'/';
        }
    }
    
    $viewUri = "https://".$this->localisation->siteAccess;
    
    if( strcmp($baseLocalisation->module, 'view') != 0 || !$baseLocalisation->has_craft ){
        $viewUri .= "/view?id=".$baseLocalisation->id;
    }
    else {
        $viewUri .= $baseLocalisation->url;
    }
    
    include $this->module->getDesignFile();
}