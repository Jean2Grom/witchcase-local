<?php 
/** 
 * @var WC\Cauldron $this 
 * @var string $input 
 */

?>

<div class="file-edit-container">
    <?php if( $this->exist() ): ?>
        <input  type="hidden" 
                name="<?=$input?>[ID]" 
                value="<?=$this->id ?>" />
    <?php endif; ?>

    <input  type="hidden" 
                name="<?=$input?>[type]" 
                value="wc-connexion" />


    <legend>email</legend>
    <?php if( $this->content('email')?->exist() ): ?>
        <input  type="hidden" 
                name="<?=$input.'[content][0][ID]'?>" 
                value="<?=$this->content('email')?->id ?>" />
    <?php endif; ?>
    <input  type="hidden" 
            name="<?=$input.'[content][0][name]'?>" 
            value="email" />
    <input  type="hidden" 
            name="<?=$input.'[content][0][type]'?>" 
            value="string" />
    <input  type="email" 
            placeholder="address@domain.ext"
            name="<?=$input.'[content][0][value]'?>"
            value="<?=$this->content('email')?->value() ?? ""?>" />

    <legend>login</legend>
    <?php if( $this->content('login')?->exist() ): ?>
        <input  type="hidden" 
                name="<?=$input.'[content][1][ID]'?>" 
                value="<?=$this->content('login')?->id ?>" />
    <?php endif; ?>
    <input  type="hidden" 
            name="<?=$input.'[content][1][name]'?>" 
            value="login" />
    <input  type="hidden" 
            name="<?=$input.'[content][1][type]'?>" 
            value="string" />
    <input  type="text" 
            name="<?=$input.'[content][1][value]'?>"
            value="<?=$this->content('login')?->value() ?? ""?>" />

    <legend>password</legend>
    <?php if( $this->content('pass_hash')?->exist() ): ?>
        <input  type="hidden" 
                name="<?=$input.'[content][2][ID]'?>" 
                value="<?=$this->content('pass_hash')?->id ?>" />
    <?php endif; ?>
    <input  type="hidden" 
            name="<?=$input.'[content][2][name]'?>" 
            value="pass_hash" />
    <input  type="hidden" 
            name="<?=$input.'[content][2][type]'?>" 
            value="string" />
    <input  type="password"
            name="<?=$input?>[content][2][value]"
            value="<?=$this->content('pass_hash')?->value() ?? ""?>" />  

    <legend>confirm password</legend>
    <input  type="password"
            name="<?=$input?>[content][2][confirm_value]"
            value="<?=$this->content('pass_hash')?->value() ?? ""?>" />  


</div>
