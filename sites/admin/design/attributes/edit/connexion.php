<input  type="hidden"
        name="<?=$this->tableColumns['id']?>"
        value="<?=$this->values['id'] ?>" />

<h2>Nom</h2>
<p>
    <input  type="text" 
            name="@_<?=$this->type.'#name__'.$this->name?>"
            value="<?=$this->values['name']?>" />
</p>

<h2>email</h2>
<p>
    <input  type="email" 
            name="@_<?=$this->type.'#email__'.$this->name?>"
            value="<?=$this->values['email']?>" />
</p>

<h2>login</h2>
<p>
    <input  type="text" 
            name="@_<?=$this->type.'#login__'.$this->name?>"
            value="<?=$this->values['login']?>" />
</p>

<h2>password</h2>
<p>
    <input  type="password" 
            name="@_<?=$this->type.'#password__'.$this->name?>"
            value="" />
</p>

<h2>confirm password</h2>
<p>
    <input  type="password" 
            name="@_<?=$this->type.'#password_confirm__'.$this->name?>"
            value="" />
</p>

<h2>Profile(s)</h2>
<p>
    <select multiple
            name="@_<?=$this->type.'#profiles__'.$this->name.'[]'?>">
        <?php foreach( $sitesProfiles as $site => $siteProfileList ): ?>
            <optgroup label="<?=$site?>">
                <?php foreach( $siteProfileList as $profile ): ?>
                    <option <?php if( in_array($profile->id, $this->values['profiles']) ): ?>
                                selected
                            <?php endif; ?>
                            value="<?=$profile->id?>">
                        <?=$profile->name?>
                    </option>
                <?php endforeach; ?>
            </optgroup>
        <?php endforeach; ?>
    </select>
</p>