<?php
namespace WC;

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
            $this->log->error($e->getMessage(), true);
        }
    }
    
    public function injest()
    {
        try {
            $this->request  = new Request( $this );
            $this->website  = $this->request->getWebsite();
            $this->user     = new User( $this );

            $this->website->summonWitches();
        }
        catch (\Exception $e){
            $this->log->error($e->getMessage(), true);
        }
        
        return $this;
    }
    
    public function run()
    {
        try {
            $this->website->sabbath();
            $this->website->display();        
        }
        catch (\Exception $e){
            $this->log->error($e->getMessage(), true);
        }
        
        $this->debug->display();
        
        return $this;
    }
    
    public function debug( $variable, $userPrefix='', $depth=1 ){
        return $this->debug->dump( $variable, $userPrefix, $depth );
    }
    
    public function dump( $variable, $userPrefix='', $depth=10 ){
        return $this->debug->dump( $variable, $userPrefix, $depth );
    }
}
