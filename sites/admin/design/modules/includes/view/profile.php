<div class="box ">
    <h3>
        <i class="fas fa-user"></i> <?=$profile->name?>
    </h3>
    <p><em><?=$profile->site != '*'? $profile->site: "All sites" ?></em></p>

    <table>
        <thead>
            <tr>
                <th>Module</th>
                <th>Status</th>
                <th>Position Witch</th>
                <th>Position Rules</th>
                <th>Custom</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach( $profile->policies as $policy ): ?>
                <tr>
                    <td>
                        <?=$policy->module ?>
                    </td>
                    <td>
                        <?=$policy->statusLabel ?>
                    </td>
                    <td>
                        <?php if( !empty($policy->positionId) ): ?>
                            <a href="<?=$this->wc->website->getUrl("/view?id=".$policy->positionId) ?>"
                               target="_blank">
                                <?=$policy->positionName ?? $policy->positionId ?>
                            </a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?=$policy->position_rules['ancestors']? "Parents": "" ?>
                        <?=$policy->position_rules['self']? "Self": "" ?>
                        <?=$policy->position_rules['descendants']? "Descendants": "" ?>
                    </td>
                    <td>
                        <?=$policy->custom_limitation ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="box__actions">
       <button data-id="<?=$profile->id ?>" 
               class="delete-profile-action">
           Supprimer
       </button>
       <button data-href="<?=$editProfileHref.$profile->id ?>" 
               class="edit-profile-action">
           Modifier
       </button>
   </div>

</div>
