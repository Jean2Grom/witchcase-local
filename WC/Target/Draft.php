<?php
namespace WC\Target;

use WC\Target;
use WC\TargetStructure;

class Draft extends Target 
{
    const TYPE      = 'draft';
    const DB_FIELDS = [
        "`content_key` int(11) DEFAULT NULL",
    ];
    const ELEMENTS = [ 
        'content_key',
    ];
    
    var $content_key;
    
    function createDraft(){
        return clone $this;
    }
    
    function getDraft(){
        return $this;
    }
    
    function publish()
    {
        $this->wc->db->begin();
        try {
            $structure      = new TargetStructure( $this->wc, $this->structure->name, Content::TYPE );
            
            if( !$this->content_key )
            {
                $newContentId   = $structure->createTarget($this->name);
                $data           = [ 'id' => $newContentId, 'name' => $this->name ];
            }
            else 
            {
                $craftData  = $this->wc->website->witchCrafting->getCraftDataFromIds($structure->table, [ $this->content_key ]);
                $data       = array_values($craftData)[0] ?? [];
            }
            
            $content                = Target::factory( $this->wc, $structure, $data );
            
            if( $this->content_key ){
                $content->archive( true );
            }
            
            $content->name          = $this->name;
            $content->attributes    = $this->attributes;            
            $content->save();
            
            foreach( $this->getWitches() as $witch ){
                $witch->edit(['target_table' => $structure->table, 'target_fk' => $newContentId]);
            }
            
            $changedTargets                                                 = $this->wc->website->changedTargets[ $this->structure->table ] ?? [];
            $changedTargets[ $this->id ]                                    = [ 'table' => $content->structure->table, 'id' => $content->id ];
            $this->wc->website->changedTargets[ $this->structure->table ]   = $changedTargets;
            
            $this->delete( false );            
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