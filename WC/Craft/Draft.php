<?php
namespace WC\Craft;

use WC\Craft;
use WC\Structure;
use WC\DataAccess\Craft as CraftDA;

class Draft extends Craft 
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
            $structure      = new Structure( $this->wc, $this->structure->name, Content::TYPE );
            
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
    
    private function publishNewContent( Structure $structure )
    {
        $content        = Craft::factory( $this->wc, $structure );
        
        $content->name          = $this->name;
        $content->attributes    = $this->attributes;            
        $content->save();
        
        foreach( $this->getWitches() as $witch ){
            $witch->edit([ 'craft_table' => $structure->table, 'craft_fk' => $content->id ]);
        }
        
        return $content;
    }
    
    private function publishUpdatedContent( Structure $structure, array $data )
    {
        $content = Craft::factory( $this->wc, $structure, $data );
            
        $content->archive( true );
            
        $content->name          = $this->name;
        $content->attributes    = $this->attributes;            
        $content->save();
        
        return $content;
    }
    
    private function publishRestoredContent( Structure $structure )
    {
        $content = Craft::factory( $this->wc, $structure );
        
        $content->name          = $this->name;
        $content->attributes    = $this->attributes;            
        $content->save();
        
        foreach( $this->getWitches(Archive::TYPE) as $witch ){
            $witch->edit(['craft_table' => $structure->table, 'craft_fk' => $content->id]);
        }
        
        CraftDA::update( $this->wc, $this->structure->table, ['content_key' => $content->id], ['content_key' => $this->content_key] );
        CraftDA::update( $this->wc, Archive::TYPE.'__'.$this->structure->name, ['content_key' => $content->id], ['content_key' => $this->content_key] );
        
        return $content;
    }
    
    function remove()
    {
        if( !$this->content_key ){
            foreach( $this->getWitches() as $witch ){
                $witch->edit([ 'craft_table' => null, 'craft_fk' => null ]);
            }
        }
        
        return $this->delete();
    }
}