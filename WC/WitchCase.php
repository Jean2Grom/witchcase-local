<?php
namespace WC;

use WC\DataAccess\WitchSummoning;

/**
 * Handler and container of this env
 *
 * @author raoul
 */
class WitchCase 
{
    /** @var Debug */
    var $debug;
    
    /** @var Configuration */
    var $configuration;
    
    /** @var Log */
    var $log;
    
    /** @var Database */
    var $db;
    
    /** @var Cache */
    var $cache;
    
    /** @var Request */
    var $request;
    
    /** @var Website */
    var $website;
    
    /** @var User */
    var $user;
    
    /** @var Cairn */
    var $cairn;
    
    public int $depth;
    
    public function __construct() 
    {
        try {
            $this->debug            =   new Debug( $this ); 
            $this->configuration    =   new Configuration( $this );
            $this->log              =   new Log( $this );
            $this->db               =   new Database( $this );
            $this->cache            =   new Cache( $this );
        }
        catch (\Exception $e){
            $this->log->error($e->getMessage(), true, [ 'file' => $e->getFile(), 'line' => $e->getLine() ]);
        }
    }
    
    public function injest(): self
    {
        try {
            $this->depth    = WitchSummoning::getDepth( $this );
            $this->request  = new Request( $this );
            $this->website  = $this->request->getWebsite();
            $this->cairn    = $this->website->getCairn();
            $this->user     = new User( $this );
            
            $this->cairn->summon();
        }
        catch (\Exception $e){
            $this->log->error($e->getMessage(), true, [ 'file' => $e->getFile(), 'line' => $e->getLine() ]);
        }
        
        return $this;
    }
    
    public function run(): self
    {
        try {
            $this->cairn->sabbath();
            $this->website->display();        
        }
        catch (\Exception $e){
            $this->log->error($e->getMessage(), true, [ 'file' => $e->getFile(), 'line' => $e->getLine() ]);
        }
        
        $this->debug->display();
        
        return $this;
    }
    
    public function debug( mixed $variable, string $userPrefix='', int $depth=1 ){
        return $this->debug->dump( $variable, $userPrefix, $depth );
    }
    
    public function dump( mixed $variable, string $userPrefix='', int $depth=10 ){
        return $this->debug->dump( $variable, $userPrefix, $depth );
    }
    
    public function witch( ?string $witchName=null ){
        return $this->cairn->witch( $witchName );
    }
}
