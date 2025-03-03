<?php 
/** 
 * @var WC\Cauldron $this 
 * @var string $input 
 */
?>

<div class="fieldsets-container">
    <legend>email</legend>
    <?php if( $this->content('email')?->exist() ): ?>
        <input  
            name="<?=$input?>[content][email][ID]" 
            value="<?=$this->content('email')?->id ?>" 
            type="hidden" 
        />
    <?php endif; ?>
    <!--input  
        name="<?=$input?>[content][email][name]" 
        value="email" 
        type="hidden" 
    />
    <input  
        name="<?=$input?>[content][email][type]" 
        value="string" 
        type="hidden" 
    /-->
    <input  
        name="<?=$input?>[content][email][value]"
        value="<?=$this->content('email')?->value() ?? ""?>" 
        type="email" 
        placeholder="address@domain.ext"
    />

    <legend>login</legend>
    <?php if( $this->content('login')?->exist() ): ?>
        <input  
            name="<?=$input?>[content][login][ID]" 
            value="<?=$this->content('login')?->id ?>" 
            type="hidden" 
        />
    <?php endif; ?>
    <!--input 
        name="<?=$input?>[content][login][name]" 
        value="login" 
        type="hidden" 
    />
    <input 
        name="<?=$input?>[content][login][type]" 
        value="string" 
        type="hidden" 
    /-->
    <input   
        name="<?=$input?>[content][login][value]" 
        value="<?=$this->content('login')?->value() ?? ""?>" 
        type="text"
    />

    <legend>password</legend>
    <?php if( $this->content('pass_hash')?->exist() ): ?>
        <input  
            name="<?=$input?>[content][pass_hash][ID]" 
            value="<?=$this->content('pass_hash')?->id ?>" 
            type="hidden"
        />
    <?php endif; ?>
    <!--input  
        name="<?=$input?>[content][password][name]" 
        value="pass_hash" 
        type="hidden"
    />
    <input 
        name="<?=$input?>[content][password][type]" 
        value="string" 
        type="hidden" 
    /-->
    <input 
        name="<?=$input?>[content][password][value]"
        value="<?=$this->content('pass_hash')?->value()? "xxxxxxxxxxxxxxxxxx" : ""?>" 
        type="password" 
    />  

    <legend>confirm password</legend>
    <input 
        name="<?=$input?>[content][password][confirm_value]" 
        value="<?=$this->content('pass_hash')?->value()? "yyyyyyyyyyyyyyyyyy" : ""?>" 
        type="password" 
    />
</div>