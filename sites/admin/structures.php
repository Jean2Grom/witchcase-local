<?php /** @var WC\Module $this */

use WC\Structure;
use WC\Attribute;
use WC\Datatype\ExtendedDateTime;
use WC\Tools;

if( $this->wc->request->param("publishStructure") ){
    $action = "publishStructure";
}
elseif( $this->wc->request->param("deleteStructures") ){
    $action = "deleteStructures";
}
elseif( $this->wc->request->param("view", 'get') ){
    $action = "viewStructure";
}
elseif( $this->wc->request->param("createStructure") 
        || $this->wc->request->param("currentAction") === "creatingStructure"
){
    $action = "createStructure";
}
elseif( $this->wc->request->param("edit", 'get')
        ||  $this->wc->request->param("deleteAttribute")
        ||  $this->wc->request->param("addAttribute")
){
    $action = "editStructure";
}
else {
    $action = "listStructures";
}

$messages = [];
if( $action === "publishStructure" )
{
    $structureName      = $this->wc->request->param("edit", 'get');
    $structure          = new Structure( $this->wc,  $structureName );    
    $attributesPost     = $this->wc->request->param("attributes", 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $attributesList     = Attribute::list();
    
    $attributes = [];
    foreach( $attributesPost as $attributesPostData )
    {
        $attributeType  = $attributesPostData['type'];
        $attributeClass = $attributesList[ $attributeType ];
        
        $parameters = [];
        if( isset($attributesPostData['parameters']) && is_array($attributesPostData['parameters']) ){
            $parameters = $attributesPostData['parameters'];
        }
        
        $attributeName  = Tools::cleanupString($attributesPostData['name']);
        
        $attribute      = new $attributeClass(
                                $this->wc,
                                $attributeName,
                                $parameters
                        );
        
        $attributes[ $attributeName ] = $attribute;
    }
        
    if( !$structure->update($attributes) )
    {
        $messages[] = "Publication failed, please try again";
        $action     = "editStructure";
    }
    else 
    {
        $messages[] = "Publication of structure ".$structure->name." successfull";
        $action     = "listStructures";
    }
}

if( strcmp($action, "createStructure") == 0 )
{
    $structuresData = Structure::listStructures( $this->wc );
    
    if( $this->wc->request->param("currentAction") === "creatingStructure" )
    {
        $nextStep = true;
        $namePost = $this->wc->request->param("name");
        
        if( !$namePost )
        {
            $nextStep = false;
            $messages[] = "Vous devez saisir un nom valide pour votre structure.";
        }
        
        if( $nextStep )
        {
            $name   = Tools::cleanupString( $namePost );
            
            if( in_array($name, array_keys($structuresData)) )
            {
                $nextStep = false;
                $messages[] = "Le nom que vous avez saisi est déjà utilisé, veuillez en saisir un autre.";
            }
        }
        
        if( $nextStep )
        {
            Structure::create($this->wc, $name);
            
            $queryParams = [ "edit" => $name ];
            
            $structureCopyPost = $this->wc->request->param("structureCopy");
            
            if( $structureCopyPost ){
                $queryParams = [ "base" => $structureCopyPost ];
            }
            
            header( 'Location: '.$this->witch->url($queryParams, $this->wc->website) );
            exit;
        }
    }
    
    $this->view('structures/create.php');
}

if( $action === "editStructure" )
{
    $structureName  = $this->wc->request->param("edit", 'get');
    
    // TODO Conf reading ?
    $attributesList = Attribute::list();
    
    $attributes = [];
    if( $this->wc->request->param("currentAction") !== "editingStructure" )
    {
        $baseStructure = $this->wc->request->param("base", 'get');
        
        if( $baseStructure ){
            $structure = new Structure( $this->wc, $baseStructure );
        }
        else {
            $structure  = new Structure( $this->wc, $structureName );
        }
        
        foreach( $structure->attributes() as $attributeName => $attributeData ){
            $attributes[ $attributeName ] = $attributeData;
        }
    }
    else
    {
        $deleteAttributePost    =   $this->wc->request->param("deleteAttribute", 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? [];
        $attributesPost         =   $this->wc->request->param("attributes", 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? [];
        
        foreach( $attributesPost as $indice => $attributePostData ){
            if( !isset($deleteAttributePost[ $indice ]) )
            {
                $attributeType  = $attributePostData['type'];
                $attributeClass = $attributesList[ $attributeType ];
                
                if( isset($attributePostData['parameters']) ){
                    $parameters = $attributePostData['parameters'];
                }
                else {
                    $parameters = [];
                }
                
                $attributes[ $attributePostData['name'] ] = [ 'class' => $attributeClass ];
            }
        }
        
        if( $this->wc->request->param("addAttribute") )
        {
            $attributeType  = $this->wc->request->param("addAttributType");
            $attributeClass = $attributesList[ $attributeType ];
            
            $attributes[ "Nouvel Attribut ".$attributeType ] = [ 'class' => $attributeClass ];
        }
    }
    
    $viewHref   = $this->witch->url([ 'view' => $structureName ]);
    
    $this->view('structures/edit.php');
}

if( $action === "viewStructure" )
{
    $structureName      = $this->wc->request->param('view');
    $structure          = new Structure( $this->wc, $structureName );
    
    $creationDateTime   = $structure->getLastModificationTime();
    $attributes         = $structure->attributes();
    $archivedAttributes = [];
    
    $modificationHref   = $this->witch->url([ 'edit' => $structure->name ]);
    
    $this->view('structures/view.php');
}

if( $action === "deleteStructures" )
{
    $structureName = $this->wc->request->param("structure");
    
    if( $structureName )
    {            
        $structure = new Structure( $this->wc,  $structureName );
        
        if( !$structure->delete() ){
            $messages[] = "Deletion of ".$structureName." failed";
        }
        else {
            $messages[] = "Structure ".$structureName." successfully deleted";              
        }
    }
    
    $action = "listStructures";
}

if( $action === "listStructures" )
{
    $structures = Structure::listStructures( $this->wc, true );
    $count      = count($structures);
    
    foreach( $structures as $key => $value )
    {
        $structures[ $key ]['viewHref']  =   $this->witch->url([ 'view' => $value['name'] ]);
        $structures[ $key ]['creation']  =   new ExtendedDateTime($value['created']);
    }
    
    $this->view();
}
