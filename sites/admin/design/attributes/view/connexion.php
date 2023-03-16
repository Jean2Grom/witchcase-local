<p>
    <strong>nom&nbsp;:</strong>
    <?=$this->values['name']?>
</p>
<p>
    <strong>email&nbsp;:</strong>
    <?=$this->values['email']?>
</p>
<p>
    <strong>login&nbsp;:</strong>
    <?=$this->values['login']?>
</p>

<strong>profile(s)&nbsp;:</strong>
<ul>
    <?php foreach( $sitesProfiles as $site => $siteProfileList ): ?>
        <?php foreach( $siteProfileList as $profile ): ?>
            <?php if( in_array($profile->id, $this->values['profiles']) ): ?>
                <li>
                    [<?=$site?>]
                    <?=$profile->name ?>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endforeach; ?>
</ul>