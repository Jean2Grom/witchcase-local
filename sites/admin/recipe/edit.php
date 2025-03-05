<?php /** @var WC\Module $this */

use WC\Cauldron\Ingredient;

$possibleActionsList = [
    'save',
];

$action = $this->wc->request->param('action');
if( !in_array($action, $possibleActionsList) ){
    $action = false;
}

$recipeName = $this->wc->request->param('recipe');
if( $recipeName ){
    $recipe = $this->wc->configuration->recipe( $recipeName );
}

if( !$recipe )
{
    $this->wc->user->addAlert([
        'level'     =>  'error',
        'message'   =>  "Recipe not found"
    ]);
    header( 'Location: '.$this->wc->website->getFullUrl('recipe') );
    exit();
}

$recipes     = $this->wc->configuration->recipes();
$ingredients    = Ingredient::list();

$possibleTypes = [];
foreach( $ingredients as $ingredient ){
    $possibleTypes[ $ingredient ] = $ingredient;
}
foreach( $recipes as $recipeItem ){
    $possibleTypes[ $recipeItem->name ] = $recipeItem->name;
}

$globalRequireInputPrefix = "GLOBAL_RECIPE_REQUIREMENTS";

switch( $action )
{
    case 'save':

        if( $this->wc->request->param("recipe") !== $recipe->name ){
            $this->wc->user->addAlert([
                'level'     =>  'error',
                'message'   =>  "Recipe mismatch"
           ]);        
        }
        else 
        {
            $inputs = $this->wc->request->inputs();

            $composition = [];
            foreach( array_keys($inputs) as $inputName ){
                if( substr($inputName, -5) === "-name" && strlen($inputName) > 5 )
                {
                    $name = substr($inputName, 0, -5);
                    $type = $inputs[ $name."-type" ] ?? null;

                    if( isset($possibleTypes[ $type ]) ){
                        $composition[] = [
                            "mandatory" => !empty($inputs[ $name."-mandatory" ]),
                            "name"      => $name,
                            "type"      => $type,
                            "require"   => getRequire( $inputs, $name ),
                        ];
                    }
                }
            }

            $recipe->name        = $this->wc->request->param("name");
            $recipe->require     = getRequire( $inputs, $globalRequireInputPrefix );
            $recipe->composition = $composition;

            if( !$recipe->save($this->wc->request->param("file")) ){
                $this->wc->user->addAlert([
                    'level'     =>  'error',
                    'message'   =>  "Recipe update failed"
               ]);    
            }
            else 
            {
                $this->wc->user->addAlert([
                    'level'     =>  'success',
                    'message'   =>  "Recipe \"".$possibleTypes[ $recipe->name ]."\" updated"
               ]);
               header( 'Location: '.$this->wc->website->getFullUrl('recipe/view', ['recipe' => $recipe->name]) );
               exit();
            }
        }
    break;
}

function getRequire( $inputs, $name )
{
    $require    = [];
    if( !empty($inputs[ $name."-accepted" ]) ){
        $require['accept'] = $inputs[ $name."-accepted" ];
    }
    if( !empty($inputs[ $name."-refused" ]) ){
        $require['refuse'] = $inputs[ $name."-refused" ];
    }
    if( $inputs[ $name."-min" ] > 0 ){
        $require['min'] = $inputs[ $name."-min" ];
    }
    if( $inputs[ $name."-max" ] > -1 ){
        $require['max'] = $inputs[ $name."-max" ];
    }

    return $require;
}

$this->view();