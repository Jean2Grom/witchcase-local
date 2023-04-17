<?php
use WC\TargetStructure;

use WC\Structure;
use WC\DataTypes\ExtendedDateTime;

if( filter_has_var(INPUT_POST, "publishStructure") ){
    $action = "publishStructure";
}
elseif( filter_has_var(INPUT_POST, "deleteStructures") ){
    $action = "deleteStructures";
}
elseif( filter_has_var(INPUT_GET, "view") ){
    $action = "viewStructure";
}
elseif( filter_has_var(INPUT_POST, "createStructure") 
        || strcmp( filter_input(INPUT_POST, 'currentAction'), "creatingStructure" ) == 0
){
    $action = "createStructure";
}
elseif( filter_has_var(INPUT_GET, "edit") 
        ||  filter_has_var(INPUT_POST, "deleteAttribute")
        ||  filter_has_var(INPUT_POST, "addAttribute")
){
    $action = "editStructure";
}
else {
    $action = "listStructures";
}


$messages = [];
$baseUri  = $this->witch->uri;

if( strcmp($action, "publishStructure") == 0 )
{
    $structureName      = filter_input( INPUT_GET, 'edit' );
    $structure          = new TargetStructure( $this->wc,  'content_'.$structureName );
    
    $attributesPost =   filter_input(   
        INPUT_POST,
        "attributes",
        FILTER_DEFAULT,
        FILTER_REQUIRE_ARRAY
    );
    
    $attributesList                  = [];
    $attributeNameSpaceClassPrefix  = "WC\\Attribute\\";
    $attributeNameSpaceClassSuffix  = "Attribute";
    foreach( get_declared_classes() as $className ){
        if( substr($className, 0, strlen($attributeNameSpaceClassPrefix) ) == $attributeNameSpaceClassPrefix 
                && substr($className, -strlen($attributeNameSpaceClassSuffix) ) == $attributeNameSpaceClassSuffix 
                && defined($className.'::ATTRIBUTE_TYPE')   ){
            $attributesList[ $className::ATTRIBUTE_TYPE ] =  $className;
        }
    }
    
    $attributes = [];
    foreach( $attributesPost as $attributesPostData )
    {
        $attributeType  = $attributesPostData['type'];
        $attributeClass = $attributesList[ $attributeType ];
        
        $parameters = [];
        if( isset($attributesPostData['parameters']) && is_array($attributesPostData['parameters']) ){
            $parameters = $attributesPostData['parameters'];
        }
        
        $attributeName  = WC\Witch::cleanupString($attributesPostData['name']);
        
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
    if( strcmp(filter_input(INPUT_POST, "currentAction"), "creatingStructure") == 0 )
    {
        $nextStep = true;
        $namePost = filter_input(INPUT_POST, "name");
        
        if( !$namePost )
        {
            $nextStep = false;
            $messages[] = "Vous devez saisir un nom valide pour votre structure.";
        }
        
        if( $nextStep )
        {
            
            $name       = WC\Witch::cleanupString( $namePost );
            $structure  = new TargetStructure( $this->wc,  'content_'.$name );
            
            if( $structure->exist )
            {
                $nextStep = false;
                $messages[] = "Le nom que vous avez saisi est déjà utilisé, veuillez en saisir un autre.";
            }
        }
        
        if( $nextStep )
        {
            $structure->create();
            
            $queryString = "?edit=".$name;
            
            $structureCopyPost = filter_input(INPUT_POST, "structureCopy");
            
            if( $structureCopyPost ){
                $queryString .= "&base=".$structureCopyPost;
            }
            
            header( 'Location: '.$this->wc->website->getFullUrl($this->witch->url.$queryString) );
            exit;
        }
    }
    
    $structuresData = Structure::listStructures( $this->wc, true );
    
    $this->setContext('standard');
    include $this->getDesignFile('structures/create.php');
}

if( strcmp($action, "editStructure") == 0 )
{
    $structureName = filter_input( INPUT_GET, 'edit' );
    
    $attributesList                  = [];
    $attributeNameSpaceClassPrefix  = "WC\\Attribute\\";
    $attributeNameSpaceClassSuffix  = "Attribute";
    foreach( get_declared_classes() as $className ){
        if( substr($className, 0, strlen($attributeNameSpaceClassPrefix) ) == $attributeNameSpaceClassPrefix 
                && substr($className, -strlen($attributeNameSpaceClassSuffix) ) == $attributeNameSpaceClassSuffix 
                && defined($className.'::ATTRIBUTE_TYPE')   ){
            $attributesList[ $className::ATTRIBUTE_TYPE ] =  $className;
        }
    }
    
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
    $structureName      = filter_input(INPUT_GET, 'view');
    $structure          = new TargetStructure( $this->wc, 'content_'.$structureName );
    
    $creationDateTime   = $structure->createTime();
    if( $structure->isArchive )
    {
        $attributes         = $structure->archivedAttributes;
        $archivedAttributes = [];
    }
    else
    {
        $attributes         = $structure->attributes;
        $archivedAttributes = [];
    }
    
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
    $baseUriOrder           =   $baseUri;
    $baseUriArchives        =   $baseUri;
    $getSeparatorOrder      =   '?';
    $getSeparatorArchives   =   '?';
    $newSeparator           =   '&';
    $orderfield             =   filter_input(INPUT_GET, 'orderfield');
    $order                  =   filter_input(INPUT_GET, 'order');
    $displayArchives        =   filter_input(INPUT_GET, "archives");

    // Archive display links and settings
    $archives = false;
    if( strcmp($displayArchives, 'yes') == 0 )
    {
        $archives    = true;
        $archiveHref =  [   "name" => "Cacher archives", 
                            "href" => $baseUriArchives.$getSeparatorArchives."archives=no"
                        ];  
    }
    else
    {
        $archiveHref =  [   "name" => "Voir archives", 
                            "href" =>   $baseUriArchives.$getSeparatorArchives."archives=yes"
                        ];
    }


    $ordersArray =  [
                        'name'      => 'asc',
                        'created'   => 'asc',
                    ];

    if( $orderfield && isset($ordersArray[$orderfield]) 
        && $order && in_array($order, ['asc', 'desc'])
    ){
        $fieldset = "orderfield=".$orderfield."&order=".$order;

        $baseUriArchives        .= $getSeparatorArchives.$fieldset;
        $getSeparatorArchives   =  $newSeparator;
    }

    if( $displayArchives && in_array($displayArchives, ['yes', 'no']) )
    {
        $fieldset = "archives=".$displayArchives;

        $baseUriOrder      .= $getSeparatorOrder.$fieldset;
        $getSeparatorOrder  = $newSeparator;
    }

    // Ordering links and settings
    $orders = [];
    if( $orderfield && $order )
    {
        // For direct SQL ordering (if not set "priority asc" will be applied)
        if( in_array($orderfield, ['name', 'created']) 
            && in_array($order, ['asc', 'desc']) 
        ){
            $orders[$orderfield] = $order; 
        }

        // For selected Ordering link to be desc
        if( isset($ordersArray[$orderfield]) 
            && strcmp($order, 'asc') == 0
        ){
            $ordersArray[$orderfield] = 'desc';
        }
    }

    $baseUriOrder .= $getSeparatorOrder.'orderfield=';
    $headers =  [
                    'Nom'                   => $baseUriOrder.'name&order='.$ordersArray['name'], 
                    'Archive'               => false, 
                    'Quantité Brouillons'   => false,
                    'Quantité Contenus'     => false,
                    'Quantité Archives'     => false,
                    'Création'              => $baseUriOrder.'created&order='.$ordersArray['created'], 
                    'Modifier'              => false, 
                ];

    if( !$archives ){
        unset($headers['Archive']);
    }

    $structuresListData = Structure::listStructures( $this->wc, $archives, $orders);
    $count              = count($structuresListData);

    $structures = [];
    foreach( $structuresListData as $valueArray )
    {
        $structure          = $valueArray['name'];
        $creationDateTime   = new ExtendedDateTime($valueArray['created']);
        $countArray         = Structure::countElements( $this->wc, $structure );

        $displayValues  =   [   "name"          => $structure, 
                                "viewHref"      => $baseUri."?view=".$structure, 
                                "draftCount"    => $countArray['draft'], 
                                "contentCount"  => $countArray['content'], 
                                "archiveCount"  => $countArray['archive'], 
                                "modifyHref"    => $baseUri."?edit=".$structure, 
                                "creation"      => $creationDateTime
                            ];

        if( $archives )
        {
            if( $valueArray['is_archive'] )
            {
                $isArchive = 'oui';
                $displayValues["modifyHref"] = false;
            }
            else
            {   $isArchive = 'non'; }

            $displayValues["isArchive"] = $isArchive;
        }

        $structures[]   =   $displayValues;

    }

    $this->setContext('standard');

    include $this->getDesignFile();
}
