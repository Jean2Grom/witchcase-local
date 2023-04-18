<?php
namespace WC\Datatype;


class ExtendedDateTime extends \DateTime 
{
    function sqlFormat()
    {
        return $this->format('Y-m-d H:i:s');
    }
        
    function frenchFormat( $time=false )
    {
        if( !isset($this->frenchFormatDate) )
        {
            $mois   =   array(  'janvier', 
                                'février', 
                                'mars', 
                                'avril', 
                                'mai', 
                                'juin', 
                                'juillet', 
                                'août', 
                                'septembre', 
                                'octobre', 
                                'novembre', 
                                'décembre'
                        );
            $jour   =   array(  'dimanche', 
                                'lundi', 
                                'mardi', 
                                'mercredi', 
                                'jeudi', 
                                'vendredi', 
                                'samedi'
                        );
            $this->frenchFormatDate =   $jour[$this->format('w')].' ';
            $this->frenchFormatDate .=  date('j').' ';
            $this->frenchFormatDate .=  $mois[$this->format('n')-1].' ';
            $this->frenchFormatDate .=  $this->format('Y');
        }
        
        if( !$time ){
            return $this->frenchFormatDate;
        }
        
        $this->frenchFormatDateTime =   $this->frenchFormatDate;
        $this->frenchFormatDateTime .=  " à ".$this->format('H:i:s');
        
        return $this->frenchFormatDateTime;
    }
}
