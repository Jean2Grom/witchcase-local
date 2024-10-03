<?php /** @var WC\Module $this */

use WC\Tools;

$action = Tools::filterAction(
    $this->wc->request->param('action'),
    [
        'move', 
        'copy',
        'edit-priorities',
    ], 
);

$positionRel = Tools::filterAction(
    $this->wc->request->param('positionRel'),
    [
        'before', 
        'after',
    ], 
);

$positionRef =  $this->wc->request->param('positionRef', null, FILTER_VALIDATE_INT);

if( !$this->witch('origin') || !$this->witch('destination') )
{
    $this->wc->user->addAlert([
        'level'     =>  'error',
        'message'   =>  "Error, witch unidentified"
    ]);
    
    header( 'Location: '.$this->wc->website->getFullUrl() );
    exit();
}

switch( $action )
{
    case 'move':
        $moveAction = !$this->witch('destination')->isMotherOf($this->witch('origin'));

        if( $moveAction && !$this->witch('origin')->moveTo($this->witch('destination')) )
        {
            $this->wc->user->addAlert([
                'level'     =>  'error',
                'message'   =>  "Error, move canceled"
            ]);
            break;
        }
        
        if( $positionRef && $positionRel )
        {
            if( $positionRel === 'before' )
            {
                $daughtersArray     = array_reverse( $this->witch('destination')->daughters() );
                $priorityInterval   = 100;
                $priority           = 0;
            }
            else
            {
                $daughtersArray     = $this->witch('destination')->daughters();    
                $priorityInterval   = -100;
                $priority           = ( count($daughtersArray) + 1 ) * $priorityInterval;
            }

            foreach( $daughtersArray as $daughter )
            {
                if( $daughter->id !== $this->witch('origin')->id )
                {
                    $daughter->edit([ 'priority' => $priority ]);
                    $priority += $priorityInterval;
                }

                if( $daughter->id === $positionRef )
                {
                    $this->witch('origin')->edit([ 'priority' => $priority ]);
                    $priority += $priorityInterval;
                }
            }
        }

        $this->wc->user->addAlert([
            'level'     =>  'success',
            'message'   =>  "Witch was moved"
        ]);
        
        header( 'Location: '.$this->wc->website->getFullUrl('view', [ 'id' =>  $this->witch('destination')->id ]) );
        exit();    
    break;

    case 'copy':
        if( !$this->witch('origin')->copyTo($this->witch('destination')) )
        {
            $this->wc->user->addAlert([
                'level'     =>  'error',
                'message'   =>  "Error, copy canceled"
            ]);
            break;
        }
        
        if( $positionRef && $positionRel )
        {
            if( $positionRel === 'before' )
            {
                $daughtersArray     = array_reverse( $this->witch('destination')->daughters() );
                $priorityInterval   = 100;
                $priority           = 0;
            }
            else
            {
                $daughtersArray     = $this->witch('destination')->daughters();    
                $priorityInterval   = -100;
                $priority           = ( count($daughtersArray) + 1 ) * $priorityInterval;
            }

            foreach( $daughtersArray as $daughter )
            {
                if( $daughter->id !== $this->witch('origin')->id )
                {
                    $daughter->edit([ 'priority' => $priority ]);
                    $priority += $priorityInterval;
                }

                if( $daughter->id === $positionRef )
                {
                    $this->witch('origin')->edit([ 'priority' => $priority ]);
                    $priority += $priorityInterval;
                }
            }
        }

        $this->wc->user->addAlert([
            'level'     =>  'success',
            'message'   =>  "Witch was copied"
        ]);
        
        header( 'Location: '.$this->wc->website->getFullUrl('view', [ 'id' => $this->witch('destination')->id ]) );
        exit();    
    break;    
}

header( 'Location: '.$this->wc->website->getFullUrl('view', [ 'id' => $this->witch('destination')?->id ]) );

exit();
