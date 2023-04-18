<?php
use WC\TargetStructure;
use WC\Witch;
use WC\Attribute;

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

$this->wc->dump($action);

$messages = [];
$baseUri  = $this->witch->uri;

if( $action === "publishStructure" )
{
    $structureName      = $this->wc->request->param("edit", 'get');
    $structure          = new TargetStructure( $this->wc,  'content_'.$structureName );    
    $attributesPost     =   $this->wc->request->param("attributes", 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
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
        
        $attributeName  = Witch::cleanupString($attributesPostData['name']);
        
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
    $structuresData = TargetStructure::listStructures( $this->wc );    
    
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
            $name   = Witch::cleanupString( $namePost );
            
            if( in_array($name, array_keys($structuresData)) )
            {
                $nextStep = false;
                $messages[] = "Le nom que vous avez saisi est déjà utilisé, veuillez en saisir un autre.";
            }
        }
        
        if( $nextStep )
        {
            TargetStructure::create($this->wc, $name);
            
            $queryString = "?edit=".$name;
            
            $structureCopyPost = $this->wc->request->param("structureCopy");
            
            if( $structureCopyPost ){
                $queryString .= "&base=".$structureCopyPost;
            }
            
            header( 'Location: '.$this->wc->website->getFullUrl($this->witch->url.$queryString) );
            exit;
        }
    }
    
    $this->setContext('standard');
    include $this->getDesignFile('structures/create.php');
}

if( $action === "editStructure" )
{
    $structureName = $this->wc->request->param("edit", 'get');
    
    // TODO Conf reading ?
    $attributesList = Attribute::list();
    
    $attributes = [];
    if( !filter_has_var(INPUT_POST, "currentAction") 
        || strcmp( filter_input(INPUT_POST, "currentAction"), "editingStructure" ) != 0
    ){
        if( filter_has_var(INPUT_GET, "base") ){
            $structure = new TargetStructure( $this->wc, 'content_'.filter_input(INPUT_GET, "base") );
        }
        else {
            $structure  = new TargetStructure( $this->wc, 'content_'.$structureName );
        }
        
        foreach( $structure->attributes as $attributeName => $attributeData ){
            $attributes[ $attributeName ] = $attributeData;
        }
    }
    else
    {
        $deleteAttributePost    =   filter_input(   
            INPUT_POST,
            "deleteAttribute",
            FILTER_DEFAULT,
            FILTER_REQUIRE_ARRAY
        );
        
        if( !$deleteAttributePost ){
            $deleteAttributePost = [];
        }
        
        $attributesPost =   filter_input(   
            INPUT_POST,
            "attributes",
            FILTER_DEFAULT,
            FILTER_REQUIRE_ARRAY
        );
        
        if( !$attributesPost ){
            $attributesPost = [];
        }
        
        foreach( $attributesPost as $indice => $attributePostData ){
            if( !isset($deleteAttributePost[$indice]) )
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
        
        if( filter_has_var(INPUT_POST, "addAttribute") )
        {
            $attributeType  = filter_input(INPUT_POST, "addAttributType");
            $attributeClass = $attributesList[ $attributeType ];
            
            $attributes[ "Nouvel Attribut ".$attributeType ] = [ 'class' => $attributeClass ];
        }
    }
    
    $viewHref   = $baseUri."?view=".$structureName;
    
    $this->setContext('standard');

    include $this->getDesignFile('structures/edit.php');

}

if( strcmp($action, "viewStructure") == 0 )
{
    $structureName      = $this->wc->request->param('view');
    $structure          = new TargetStructure( $this->wc, 'content_'.$structureName );
    
    $creationDateTime   = $structure->getLastModificationTime();
    $attributes         = $structure->attributes;
    $archivedAttributes = [];
    
    $modificationHref   = $baseUri."?edit=".$structure->name;
    
    $this->setContext('standard');
    
    include $this->getDesignFile('structures/view.php');
}

if( strcmp($action, "deleteStructures") == 0 )
{
    $structuresPost =   filter_input(   INPUT_POST,
                                        "structures",
                                        FILTER_SANITIZE_STRING,
                                        FILTER_REQUIRE_ARRAY
                        );
    
    if( $structuresPost ){
        foreach( $structuresPost as $structureName )
        {
            $structure = new TargetStructure( $this->wc,  'content_'.$structureName );
            
            if( !$structure->delete() )
            {   $messages[] = "Deletion of ".$structureName." failed";  }
            else
            {   $messages[] = "Structure ".$structureName." successfully deleted";  }
        }
    }
    
    $action = "listStructures";
}

if( strcmp($action, "listStructures") == 0 )
{
    
    $structures = TargetStructure::listStructures( $this->wc );
    $count      = count($structures);
    
    foreach( $structures as $key => $value )
    {
        $structure          = $value['name'];
        $creationDateTime   = new \DateTime($value['created']);
        
        $structures[ $key ]['viewHref']  =   $baseUri."?view=".$value['name'];
        $structures[ $key ]['creation']  =   new \DateTime($value['created']);

    }

    $this->setContext('standard');

    include $this->getDesignFile();
}
