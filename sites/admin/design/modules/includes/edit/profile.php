<style>
    .policy-pattern {
        display: none;
    }
    input.profile-name {
        width: auto;
        font-weight: bold;
    }
    .policy-witch-set {
        padding: 0;
    }
        .policy-witch-set li {
            display: flex;
            align-items: center
        }
        .policy-witch-set label {
            font-size: 0.9em;
            font-weight: normal;
            margin: 0;
        }
    .policy-container textarea {
        height: 70px;
    }
</style>
<div class="box edit__profile" data-profile="<?=$profile->id?>">
   <form class="edit-profile-form" method="post" >
        <input type="hidden" name="profile-id[]" value="<?=$profile->id ?>" />
        
        <h3>
            <i class="fas fa-user"></i>
            <input type="text" 
                   class="profile-name"
                   name="profile-name[]" 
                   value="<?=$profile->name ?>" 
                   data-init="<?=$profile->name ?>" />
        </h3>
        <p>
            <em>
                Scope 
                <select name="profile-site[]" 
                        class="profile-site"
                        data-init="<?=$profile->site ?>">
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
            </em>
        </p>
        
        <table>
            <thead>
                <tr>
                    <th>Module</th>
                    <th>Status</th>
                    <th>Position Witch</th>
                    <th>Position Rules</th>
                    <th>Custom</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <tr class="policy-container policy-pattern">
                    <td>
                        <input type="hidden" name="policy-id[]" class="policy-id" value="-1" />
                        
                        <div  class="profile-site-displayed profile-site-all">
                            <select name="policy-module[all][]" data-init="*">
                                <option value="*">
                                    All modules
                                </option>
                                <?php foreach( $allSitesModulesList as $moduleItem ): ?>
                                    <option value="<?=$moduleItem ?>">
                                        <?=$moduleItem ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <?php foreach( $websitesList as $site => $website ): ?>
                            <div style="display: none;" class="profile-site-displayed profile-site-<?=$site ?>">
                                <select name="policy-module[<?=$site ?>][]" data-init="*">
                                    <option value="*">
                                        All modules
                                    </option>
                                    <?php foreach( $website->listModules() as $moduleItem ): ?>
                                        <option value="<?=$moduleItem ?>">
                                            <?=$moduleItem ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endforeach; ?>
                    </td>
                    
                    <td>
                        <div class="profile-site-displayed profile-site-all">
                            <select name="policy-status[all][]" data-init="*">
                                <option value="*">
                                    All status
                                </option>
                                <?php foreach( $statusGlobal as $statusKey => $statusLabel ): ?>
                                    <option value="<?=$statusKey ?>">
                                        <?=$statusLabel ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <?php foreach( $websitesList as $site => $website ): ?>
                            <div style="display: none;" class="profile-site-displayed profile-site-<?=$site ?>">
                                <select name="profile-status[<?=$site ?>][]" data-init="*">
                                    <option value="*">
                                        All status
                                    </option>
                                    <?php foreach( $website->get('status') as $statusKey => $statusLabel ): ?>
                                        <option value="<?=$statusKey ?>">
                                            <?=$statusLabel ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endforeach; ?>
                    </td>
                    
                    <td>
                        <button class="policy-witch">Choose Witch</button>

                        <a  style="display: none;"
                            href="<?=$this->wc->website->getUrl("/view?id=") ?>"
                            class="policy-witch-display"
                            target="_blank">                            
                        </a>
                        
                        <a  style="display: none;"
                            class="unset-policy-witch">
                            <i class="fa fa-times"></i>
                        </a>

                        <input  type="hidden" 
                                value="" 
                                name="policy-witch-id[]" 
                                class="policy-witch-id" />
                    </td>
                    <td>
                        <ul class="policy-witch-set" style="display: none;">
                            <li>
                                <input type="checkbox" 
                                       name="policy-witch-rules[]"
                                       value="ancestors" />
                                <label>Parents</label>
                            </li>
                            <li>
                                <input type="checkbox" 
                                       name="policy-witch-rules[]"
                                       value="self"
                                       checked />
                                <label>Self</label>
                            </li>
                            <li>
                                <input type="checkbox" 
                                       name="policy-witch-rules[]"
                                       value="descendants"
                                       checked />
                                <label>Descendants</label>
                            </li>
                        </ul>
                    </td>
                    <td>
                        <textarea name="policy-witch-custom[]"></textarea>
                    </td>
                    <td>
                        <input  type="hidden" 
                                name="policy-deleted[]"
                                class="policy-deleted"
                                value="" />
                        <a class="text-center policy-remove">
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>
                
                <?php foreach( $profile->policies as $policy ): ?>
                    <tr class="policy-container">
                        <td>
                            <input type="hidden" name="policy-id[]" class="policy-id" value="<?=$policy->id ?>" />
                            
                            <div    <?=($profile->site !== "*")? 'style="display: none;"' :'' ?>
                                    class="profile-site-displayed profile-site-all">
                                <select name="policy-module[all][]"
                                        data-init="<?=$policy->module ?? '*' ?>">
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
                                    class="profile-site-displayed profile-site-<?=$site ?>">
                                    <select name="policy-module[<?=$site ?>][]" 
                                            data-init="<?=$policy->module ?? '*' ?>">
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
                                    class="profile-site-displayed profile-site-all">
                                <select name="policy-status[all][]"
                                        data-init="<?=$policy->status ?? '*' ?>">
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
                                    class="profile-site-displayed profile-site-<?=$site ?>">
                                    <select name="profile-status[<?=$site ?>][]" 
                                            data-init="<?=$policy->status ?? '*' ?>">
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
                            <button <?=!empty($policy->positionId)? 'style="display: none;"': '' ?>
                                    class="policy-witch">Choose Witch</button>
                            
                            <a  <?=empty($policy->positionId)? 'style="display: none;"': '' ?>
                                href="<?=$this->wc->website->getUrl("/view?id=".$policy->positionId) ?>"
                                class="policy-witch-display"
                                target="_blank">
                                <?=!empty($policy->positionName)? $policy->positionName: '' ?>
                            </a>
                            
                            <a  <?=empty($policy->positionId)? 'style="display: none;"': '' ?>
                                class="unset-policy-witch">
                                <i class="fa fa-times"></i>
                            </a>
                            
                            <input  type="hidden" 
                                    value="<?=!empty($policy->positionId)? $policy->positionId: '' ?>" 
                                    data-init="<?=$policy->positionId ?>"
                                    name="policy-witch-id[]" 
                                    class="policy-witch-id" />
                        </td>
                        <td>
                            <ul class="policy-witch-set" <?=empty($policy->positionId)? 'style="display: none;"': '' ?>>
                                <li>
                                    <input type="checkbox" 
                                           name="policy-witch-rules[]"
                                           value="ancestors"
                                           <?=$policy->position_rules['ancestors']? "checked": "" ?> />
                                    <label>Parents</label>
                                </li>
                                <li>
                                    <input type="checkbox" 
                                           name="policy-witch-rules[]"
                                           value="self"
                                           <?=$policy->position_rules['self']? "checked": "" ?> />
                                    <label>Self</label>
                                </li>
                                <li>
                                    <input type="checkbox" 
                                           name="policy-witch-rules[]"
                                           value="descendants"
                                           <?=$policy->position_rules['descendants']? "checked": "" ?> />
                                    <label>Descendants</label>
                                </li>
                            </ul>
                        </td>
                        <td>
                            <textarea   data-init="<?=$policy->custom_limitation ?>"
                                        name="policy-witch-custom[]"><?=$policy->custom_limitation ?></textarea>                            
                        </td>
                        <td>
                            <input  type="hidden" 
                                    name="policy-deleted[]"
                                    class="policy-deleted"
                                    value="" />
                            <a class="text-center policy-remove">
                                <i class="fa fa-trash"></i>
                            </a>
                            
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="box__actions">
           <button class="view-edit-profile-toggle">
               <i class="fa fa-times"></i>
               Cancel
           </button>
           <button class="undo-profile-action">
               <i class="fa fa-undo"></i>
               Reinit
           </button>
           <button class="add-policy-action">
               <i class="fa fa-plus"></i>
               Add new policy
           </button>
           <button data-href="<?=$editProfileHref.$profile->id ?>" 
                   class="edit-profile-action">
               <i class="fas fa-save"></i>
               Save
           </button>
       </div>
   </form>
    
    
    
    
</div>