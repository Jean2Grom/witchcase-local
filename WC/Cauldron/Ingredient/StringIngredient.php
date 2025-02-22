<?php 
namespace WC\Cauldron\Ingredient;

use WC\Cauldron\Ingredient;
use WC\DataAccess\IngredientDataAccess as DataAccess;

class StringIngredient extends Ingredient
{
    const TYPE  = 'string';

    /** @var string[] */
    public array $pendingRemoveFiles = [];

    /**
     * Init function used to setup ingredient
     * @param mixed $value : if left to null, read from properties values 'value'
     * @return self
     */
    function init( mixed $value=null ): self {
        return $this->set( $value ?? (string) ($this->properties[ 'value' ] ?? "") );
    }

    /**
     * Default function to set value
     * @param mixed $value : has to be a string
     * @return self
     */
    public function set( mixed $value ): self
    {
        if( !is_null($value) && !is_string($value) ){
            $this->wc->log->error( "Try to set a non string value to ".$this->type." ingredient");
        }
        else 
        {
            if( $value !== $this->value )
            {
                $storage = $this->wc->configuration->storage();

                if( is_file($storage.'/'.$this->value()) ){
                    $this->pendingRemoveFiles[] = $this->value;
                }
            }

            $this->value = $value;
        }

        return $this;
    }

    function value(): string {
        return $this->value ?? "";
    }

    function save(): bool 
    {
        if( !parent::save() ){
            return false;
        }

        $this->removePendingFiles();

        return true;
    }

    function delete(): bool 
    {
        $this->set("");
        $this->removePendingFiles();
        
        return parent::delete();
    }

    private function removePendingFiles(): void 
    {
        $storage = $this->wc->configuration->storage();
        foreach( $this->pendingRemoveFiles as $removeFile )
        {
            if( !is_file($storage.'/'.$removeFile) ){
                continue;
            }

            if( DataAccess::searchValueCount($this, $removeFile) === 0 ){
                unlink($storage.'/'.$removeFile);
            }
        }

        return;
    }

}