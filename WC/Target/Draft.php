<?php
namespace WC\Target;

use WC\Target;
use WC\TargetStructure;
use WC\DataAccess\Target as TargetDA;

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
            
            // No content or archive exist
            if( !$this->content_key ){
                $content = $this->publishNewContent( $structure );
            }
            else 
            {
                $craftData  = $this->wc->website->witchCrafting->getCraftDataFromIds($structure->table, [ $this->content_key ]);
                $data       = array_values($craftData)[0] ?? null;
                
                if( $data ){
                    $content = $this->publishUpdatedContent( $structure, $data );
                }
                else {
                    $content = $this->publishRestoredContent( $structure );
                }
            }
            
            $this->wc->cairn->setCraft($content, $this->structure->table, $this->id);
            
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
    
    private function publishNewContent( TargetStructure $structure )
    {
        $content        = Target::factory( $this->wc, $structure );
        
        $content->name          = $this->name;
        $content->attributes    = $this->attributes;            
        $content->save();
        
        foreach( $this->getWitches() as $witch ){
            $witch->edit([ 'target_table' => $structure->table, 'target_fk' => $content->id ]);
        }
        
        return $content;
    }
    
    private function publishUpdatedContent( TargetStructure $structure, array $data )
    {
        $content = Target::factory( $this->wc, $structure, $data );
            
        $content->archive( true );
            
        $content->name          = $this->name;
        $content->attributes    = $this->attributes;            
        $content->save();
        
        return $content;
    }
    
    private function publishRestoredContent( TargetStructure $structure )
    {
        $content = Target::factory( $this->wc, $structure );
        
        $content->name          = $this->name;
        $content->attributes    = $this->attributes;            
        $content->save();
        
        foreach( $this->getWitches(Archive::TYPE) as $witch ){
            $witch->edit(['target_table' => $structure->table, 'target_fk' => $content->id]);
        }
        
        TargetDA::update( $this->wc, $this->structure->table, ['content_key' => $content->id], ['content_key' => $this->content_key] );
        TargetDA::update( $this->wc, Archive::TYPE.'__'.$this->structure->name, ['content_key' => $content->id], ['content_key' => $this->content_key] );
        
        return $content;
    }
    
    function remove()
    {
        if( !$this->content_key ){
            foreach( $this->getWitches() as $witch ){
                $witch->edit([ 'target_table' => null, 'target_fk' => null ]);
            }
        }
        
        return $this->delete();
    }
}