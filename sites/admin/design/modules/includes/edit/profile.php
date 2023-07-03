<style>
    .policy-witch-set {
        font-size: 0.9em;
    }
</style>
<div class="box ">
   <form id="edit-profile-form" method="post" >
        <h3>
            <i class="fas fa-user"></i> <?=$profile->name ?>
        </h3>
        <p><em>
            Scope 
            <select name="profile-site" id="profile-site">
                <option value="*" <?=($profile->site == "*")? 'selected': '' ?>>
                    All sites
                </option>
                <?php foreach( $websitesList as $website ): ?>
                    <option <?=($profile->site == $website->site)? 'selected': '' ?>
                            value="<?=$website->site ?>">
                        <?=$website->site ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </em></p>


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
                            <div    <?=($profile->site !== "*")? 'style="display: none;"' :'' ?>
                                    class="profile-<?=$profile->id ?>-module-all">
                                <select name="profile-module[<?=$site ?>]"
                                        data-init="<?=$policy->module ?>">
                                    <option value="*">
                                        All modules
                                    </option>
                                    <?php foreach( $allSitesModulesList as $moduleItem ): ?>
                                        <option <?=($policy->module === $moduleItem)? 'selected': '' ?>
                                                value="<?=$moduleItem ?>">
                                            <?=$moduleItem ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <?php foreach( $websitesList as $site => $website ): ?>
                                <div <?=($profile->site !== $website->site)? 'style="display: none;"' :'' ?>
                                    class="profile-<?=$profile->id ?>-module-<?=$site ?>">
                                    <select name="profile-module[<?=$site ?>]" 
                                            data-init="<?=$policy->module ?>">
                                        <option value="*">
                                            All modules
                                        </option>
                                        <?php foreach( $website->listModules() as $moduleItem ): ?>
                                            <option <?=($policy->module === $moduleItem)? 'selected': '' ?>
                                                    value="<?=$moduleItem ?>">
                                                <?=$moduleItem ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endforeach; ?>
                        </td>
                        
                        <td>
                            <div    <?=($profile->site !== "*")? 'style="display: none;"' :'' ?>
                                    class="profile-<?=$profile->id ?>-module-all">
                                <select name="profile-status[<?=$site ?>]"
                                        data-init="<?=$policy->module ?>">
                                    <option value="*">
                                        All status
                                    </option>
                                    <?php foreach( $statusGlobal as $statusKey => $statusLabel ): ?>
                                        <option <?=($policy->status === $statusKey)? 'selected': '' ?>
                                                value="<?=$statusKey ?>">
                                            <?=$statusLabel ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <?php foreach( $websitesList as $site => $website ): ?>
                                <div <?=($profile->site !== $website->site)? 'style="display: none;"' :'' ?>
                                    class="profile-<?=$profile->id ?>-status-<?=$site ?>">
                                    <select name="profile-status[<?=$site ?>]" 
                                            data-init="<?=$policy->status ?>">
                                        <option value="*">
                                            All status
                                        </option>
                                        <?php foreach( $website->get('status') as $statusKey => $statusLabel ): ?>
                                            <option <?=($policy->status === $statusKey)? 'selected': '' ?>
                                                    value="<?=$statusKey ?>">
                                                <?=$statusLabel ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endforeach; ?>                            
                        </td>
                        
                        <td>
                            <?php if( !$policy->position ): ?>
                                <button class="policy-witch">Choose Witch</button>
                                
                            <?php else: ?>
                                <a href="<?=$this->wc->website->getUrl("/view?id=".$policy->positionId) ?>"
                                   class="witch-access"
                                   target="_blank">
                                    <?=$policy->positionName ?>
                                </a>
                                
                                <a  <?=($policy->position !== false)? 'style="display: inline-block;"': '' ?>
                                    class="unset-profile-witch">
                                    <i class="fa fa-times"></i>
                                </a>
                            <?php endif; ?>
                            
                            <input  type="hidden" 
                                    value="<?=($policy->position !== false)? $policy->positionId: '' ?>" 
                                    name="profile-witch-id[]" 
                                    class="profile-witch-id" />
                        </td>
                        <td>
                            <div class="policy-witch-set">
                                <div style="display: flex;align-items: baseline;">
                                    <input type="checkbox" 
                                           name="policy-witch-rules[]"
                                           id="policy-witch-rule-ancestor"
                                           value="ancestors"
                                           <?=$policy->position_rules['ancestors']? "checked": "" ?> />
                                    <label for="policy-witch-rule-ancestor">Parents</label>
                                </div>
                                <div style="display: flex;align-items: baseline;">
                                    <input type="checkbox" 
                                           name="policy-witch-rules[]"
                                           value="self"
                                           <?=$policy->position_rules['self']? "checked": "" ?> />
                                    <label for="policy-witch-rule-ancestor">Self</label>
                                </div>
                                <div style="display: flex;align-items: baseline;">

                                    <input type="checkbox" 
                                           name="policy-witch-rules[]"
                                           value="descendants"
                                           <?=$policy->position_rules['descendants']? "checked": "" ?> />
                                    <label for="policy-witch-rule-ancestor">Descendants</label>
                                </div>
                            </div>
                        </td>
                        <td>
                            <textarea ><?=$policy->custom_limitation ?></textarea>                            
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
   </form>
    
    
    
    
</div>