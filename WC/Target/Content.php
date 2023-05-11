<?php
namespace WC\Target;

use WC\Target;
use WC\TargetStructure;

class Content extends Target 
{
    const TYPE          = 'content';    
    const DB_FIELDS     = [];
    const ELEMENTS      = [];
    
    
    function archive( bool $historyMode=false )
    {
        $this->wc->db->begin();
        try {
            $structure      = new TargetStructure( $this->wc, $this->structure->name, Archive::TYPE );
            
            $newArchiveId   = $structure->createTarget($this->name);
            $archive        = Target::factory( $this->wc, $structure );
            
            $archive->id            = $newArchiveId;
            $archive->name          = $this->name;
            $archive->content_key   = $this->id;
            $archive->attributes    = $this->attributes;
            $archive->save();
            
            if( !$historyMode )
            {
                foreach( $this->getWitches() as $witch ){
                    $witch->edit(['target_table' => $structure->table, 'target_fk' => $newArchiveId]);
                }
                
                $changedTargets                                                 = $this->wc->website->changedTargets[ $this->structure->table ] ?? [];
                $changedTargets[ $this->id ]                                    = [ 'table' => $archive->structure->table, 'id' => $archive->id ];
                $this->wc->website->changedTargets[ $this->structure->table ]   = $changedTargets;
                
                
                $this->delete( false );
            }
        }
        catch( \Exception $e )
        {
            $this->wc->log->error($e->getMessage());
            $this->wc->db->rollback();
            return false;
        }
        $this->wc->db->commit();
        
        return true;
    }
}
