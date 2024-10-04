<?php /** @var WC\Module $this */

use WC\Tools;

if( !$this->witch('origin') || !$this->witch('destination') )
{
    $this->wc->user->addAlert([
        'level'     =>  'error',
        'message'   =>  "Error, witch unidentified"
    ]);
    
    header( 'Location: '.$this->wc->website->getFullUrl() );
    exit();
}

$action = Tools::filterAction(
    $this->wc->request->param('action'),
    [
        'move', 
        'copy',
    ], 
);

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
        
        $this->wc->user->addAlert([
            'level'     =>  'success',
            'message'   =>  "Witch was moved"
        ]);
    break;

    case 'copy':
        $newWitch = $this->witch('origin')->copyTo($this->witch('destination'));
        if( !$newWitch )
        {
            $this->wc->user->addAlert([
                'level'     =>  'error',
                'message'   =>  "Error, copy canceled"
            ]);
            break;
        }
        
        $this->wc->user->addAlert([
            'level'     =>  'success',
            'message'   =>  "Witch was copied"
        ]);        
    break;    
}

if( $action )
{
    $positionRel = Tools::filterAction(
        $this->wc->request->param('positionRel'),
        [
            'before', 
            'after',
        ], 
    );
    
    $positionRef =  $this->wc->request->param('positionRef', null, FILTER_VALIDATE_INT);
    
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
            $priority           = -( count($daughtersArray) + 1 ) * $priorityInterval;
        }
        
        $this->wc->db->begin();
        $commit = true;
        try {
            foreach( $daughtersArray as $daughter )
            {
                if( $daughter->id !== ($newWitch ?? $this->witch( 'origin' ))->id )
                {
                    $daughter->edit([ 'priority' => $priority ]);
                    $priority += $priorityInterval;
                }
        
                if( $daughter->id === $positionRef )
                {
                    ($newWitch ?? $this->witch( 'origin' ))->edit([ 'priority' => $priority ]);
                    $priority += $priorityInterval;
                }
            }
        } 
        catch( \Exception $e ) 
        {            
            $this->wc->user->addAlert([
                'level'     =>  'warning',
                'message'   =>  "Priority update failed"
            ]);

            $this->wc->log->error($e->getMessage());
            $this->wc->db->rollback();
            $commit = false;
        }
        if( $commit ){
            $this->wc->db->commit();
        }
    }
}

header( 'Location: '.$this->wc->website->getFullUrl('view', [ 'id' => $this->witch('destination')->id ]) );
exit();