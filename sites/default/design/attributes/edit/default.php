<?php if( count($this->tableColumns) == 1 && count($this->values) == 1 ): ?>
    <input  type="text" 
            name="<?=array_values($this->tableColumns)[0]?>" 
            id="<?=array_values($this->tableColumns)[0]?>" 
            value="<?=htmlentities(array_values($this->values)[0])?>" />
<?php else: 
    $this->wc->dump($this->tableColumns);
    $this->wc->dump($this->values);    
endif; ?>

