<?php /** @var WC\Module $this */

use WC\Handler\StructureHandler;
use WC\Ingredient;

$possibleActionsList = [
    'publish',
];

$action = $this->wc->request->param('action');
if( !in_array($action, $possibleActionsList) ){
    $action = false;
}

$structure      = StructureHandler::createFromData($this->wc, []);

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

        if( empty($this->wc->request->param("name")) ){
            $this->wc->user->addAlert([
                'level'     =>  'error',
                'message'   =>  "Structure must have a name"
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
                    'message'   =>  "Structure creation failed"
               ]);    
            }
            else 
            {
                $this->wc->user->addAlert([
                    'level'     =>  'success',
                    'message'   =>  "Structure \"".$structure->name."\" created"
               ]);
               header( 'Location: '.$this->wc->website->getFullUrl('structure/edit', ['structure' => $structure->name]) );
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

$this->view('structure/edit');