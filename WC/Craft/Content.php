<?php
namespace WC\Craft;

use WC\Craft;
use WC\Structure;

class Content extends Craft 
{
    const TYPE          = 'content';    
    const DB_FIELDS     = [];
    const ELEMENTS      = [];
    
    
    function archive( bool $historyMode=false )
    {
        $this->wc->db->begin();
        try {
            $structure      = new Structure( $this->wc, $this->structure->name, Archive::TYPE );
            
            $newArchiveId   = $structure->createCraft($this->name);
            $archive        = Craft::factory( $this->wc, $structure );
            
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
                
                $this->wc->cairn->setCraft($archive, $this->structure->table, $this->id);
                
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
