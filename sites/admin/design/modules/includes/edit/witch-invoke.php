<?php 
    $this->addJsFile('edit-invoke.js');
?>
<div class="box edit__witch-invoke">
    <form   method="post"
            action="<?=$this->wc->website->getUrl('edit?id='.$targetWitch->id) ?>"
            id="edit-witch-invoke">
        <h3>
            <i class="fa fa-dragon"></i>
            Edit Witch Invoke Form
        </h3>
        
        <label for="witch-site">
            Site
            <em>Choose site where url have to be applied</em>
        </label>
        <select name="witch-site" 
                id="witch-site"
                data-init="<?=$targetWitch->site ?>">>
            <option value="">
                Unreachable
            </option>
            <?php foreach( $websitesList as $website ): ?>
                <option <?=($targetWitch->site === $website->site)? 'selected' :'' ?>
                        value="<?=$website->site ?>">
                    <?=$website->name ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <?php foreach( $websitesList as $site => $website ): ?>
            <div <?=($targetWitch->site !== $website->site)? 'style="display: none;"' :'' ?>
                class="witch-invoke__part witch-invoke__part-<?=$site ?>">
                
                <label for="witch-invoke-<?=$site ?>">
                    Module to invoke
                    <em>Choose module to be executed</em>
                </label>
                <select name="witch-invoke[<?=$site ?>]" 
                        id="witch-invoke-<?=$site ?>"                                           
                        data-init="<?=$targetWitch->invoke ?>">
                    <option value="">No module</option>
                    <?php foreach( $website->listModules() as $moduleItem ): ?>
                        <option <?=($targetWitch->invoke === $moduleItem)? 'selected': '' ?>
                                value="<?=$moduleItem ?>"><?=$moduleItem ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label for="witch-status-<?=$site ?>">
                    Status limitation
                    <em>You can add a minimum status level access</em>
                </label>
                <select name="witch-status[<?=$site ?>]" 
                        id="witch-status-<?=$site ?>"  
                        data-init="<?=$targetWitch->statusLevel ?>">
                    <?php foreach(  $website->get('status') as $statusKey => $statusLabel ): ?>
                        <option <?=($targetWitch->statusLevel === $statusKey)? 'selected': '' ?>
                                value="<?=$statusKey ?>"><?=$statusLabel ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label for="witch-context-<?=$site ?>">
                    Forced Context
                    <em>You can force default context here</em>
                </label>
                <select name="witch-context[<?=$site ?>]" 
                        id="witch-context-<?=$site ?>" 
                        data-init="<?=$targetWitch->context?>">
                    <option value="">Empty</option>
                    
                    <?php foreach( $website->listContexts() as $contextItem ): ?>
                        <option value="<?=$contextItem ?>"><?=$contextItem ?></option>
                    <?php endforeach; ?>
                </select>
                
            </div>
        <?php endforeach; ?>
        
        <div class="site-selected"
             <?=!$targetWitch->site? 'style="display: none;"' :'' ?>>
            <div class="auto-url-disabled"
                 <?=!$targetWitch->url? 'style="display: none;"' :'' ?>>
                <label for="witch-url">
                    URL
                    <em>Relative to site access if "Full URL" is checked, <br/>to closest parent URL if not</em>
                </label>
                <input  type="text"
                        name="witch-url"
                        id="witch-url"
                        data-init="<?=$targetWitch->url?>"
                        value="<?=$targetWitch->url ?>" />

                <label for="witch-full-url">Full URL</label>
                <input type="checkbox" 
                       id="witch-full-url" 
                       <?=$targetWitch->url ? 'checked': '' ?>
                       name="witch-full-url" />
            </div>
            
            <label for="witch-auto-url">
                Auto URL
                <em>To let witchcase automatic URL generation</em>
            </label>
            <input type="checkbox" 
                   id="witch-auto-url" 
                   <?=$targetWitch->url ? '': 'checked' ?>
                   name="witch-automatic-url" />
        </div>
    </form>
    
    <div class="box__actions">
        <button class="trigger-action" 
                data-target="edit-witch-invoke"
                data-action="save-witch-invoke">Save</button>        
        <button class="edit-invoke-reinit">Reinit Form</button>        
        <button class="view-witch-invoke__edit-witch-invoke__toggle">Cancel</button>
    </div>
</div>
