<?php /** @var WC\Module $this */

use WC\Ingredient;

$possibleActionsList = [
    'publish',
];

$action = $this->wc->request->param('action');
if( !in_array($action, $possibleActionsList) ){
    $action = false;
}

$structureName = $this->wc->request->param('structure');
if( $structureName ){
    $structure = $this->wc->configuration->structure( $structureName );
}

if( !$structure )
{
    $this->wc->user->addAlert([
        'level'     =>  'error',
        'message'   =>  "Structure not found"
    ]);
    header( 'Location: '.$this->wc->website->getFullUrl('structure') );
    exit();
}

$structures     = $this->wc->configuration->structures();
$ingredients    = Ingredient::list();

$possibleTypes = [];
foreach( $ingredients as $ingredient ){
    $possibleTypes[ $ingredient ] = $ingredient;
}
foreach( $structures as $structureItem ){
    $possibleTypes[ $structureItem->name ] = $structureItem->name;
}

$globalRequireInputPrefix = "GLOBAL_STRUCTURE_REQUIREMENTS";

switch( $action )
{
    case 'publish':

        if( $this->wc->request->param("structure") !== $structure->name ){
            $this->wc->user->addAlert([
                'level'     =>  'error',
                'message'   =>  "Structure mismatch"
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

            $structure->name        = $this->wc->request->param("name");
            $structure->require     = getRequire( $inputs, $globalRequireInputPrefix );
            $structure->composition = $composition;

            if( !$structure->save($this->wc->request->param("file")) ){
                $this->wc->user->addAlert([
                    'level'     =>  'error',
                    'message'   =>  "Structure update failed"
               ]);    
            }
            else 
            {
                $this->wc->user->addAlert([
                    'level'     =>  'success',
                    'message'   =>  "Structure \"".$possibleTypes[ $structure->name ]."\" updated"
               ]);
               header( 'Location: '.$this->wc->website->getFullUrl('structure/view', ['structure' => $structure->name]) );
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
    if( $inputs[ $name."-max" ] > 0 ){
        $require['max'] = $inputs[ $name."-max" ];
    }

    return $require;
}

$alerts = $this->wc->user->getAlerts();

$this->view();