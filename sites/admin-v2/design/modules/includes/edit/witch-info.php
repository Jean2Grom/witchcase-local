<div class="box edit__witch-info">
    <form   method="post"
            action="<?=$this->wc->website->getUrl('edit?id='.$targetWitch->id) ?>"
            id="edit-witch-info">
        <h3 class="box-info">
            <img src="<?=$this->image('favicon.png') ?>" />
            Edit Witch Information Form
        </h3>
        
        <label for="witch-site">
            Site
        </label>
        <select name="witch-site" 
                id="witch-site"
                data-init="<?=$targetWitch->site ?>">
            <option value="">
                no site selected
            </option>
            <?php foreach( $websitesList as $website ): ?>
                <option <?=($targetWitch->site === $website->site)? 'selected' :'' ?>
                        value="<?=$website->site ?>">
                    <?=$website->name ?>
                </option>
            <?php endforeach; ?>
        </select>

        <div <?=!is_null($targetWitch->site)? 'style="display: none;"' :'' ?>
            class="witch-info__part witch-info__part-">

            <label for="witch-status-">
                Status
            </label>
            <select name="witch-status[no-site-selected]" 
                    id="witch-status-"  
                    data-init="<?=$targetWitch->statusLevel ?>">
                <?php foreach(  $this->wc->configuration->read( "global", "status" ) as $statusKey => $statusLabel ): ?>
                    <option <?=($targetWitch->statusLevel === $statusKey)? 'selected': '' ?>
                            value="<?=$statusKey ?>"><?=$statusLabel ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <?php foreach( $websitesList as $site => $website ): ?>
            <div <?=($targetWitch->site !== $website->site)? 'style="display: none;"' :'' ?>
                class="witch-info__part witch-info__part-<?=$site ?>">
                
                <label for="witch-status-<?=$site ?>">
                    Status
                </label>
                <select name="witch-status[<?=$site ?>]" 
                        id="witch-status-<?=$site ?>"  
                        data-init="<?=$targetWitch->statusLevel ?>">
                    <?php foreach(  $website->status as $statusKey => $statusLabel ): ?>
                        <option <?=($targetWitch->statusLevel === $statusKey)? 'selected': '' ?>
                                value="<?=$statusKey ?>"><?=$statusLabel ?></option>
                    <?php endforeach; ?>
                </select>                
                
                <label for="witch-invoke-<?=$site ?>">
                    Module to invoke
                </label>
                <select name="witch-invoke[<?=$site ?>]" 
                        id="witch-invoke-<?=$site ?>"                                  
                        class="witch-invoke"
                        data-init="<?=$targetWitch->invoke ?>">
                    <option value="">
                        no module to invoke
                    </option>
                    <?php foreach( $website->listModules() as $moduleItem ): ?>
                        <option <?=($targetWitch->invoke === $moduleItem)? 'selected': '' ?>
                                value="<?=$moduleItem ?>"><?=$moduleItem ?></option>
                    <?php endforeach; ?>
                </select>
                
                <!--label for="witch-context-<?=$site ?>">
                    Forced Context
                    <em>You can force default context here</em>
                </label>
                <select name="witch-context[<?=$site ?>]" 
                        id="witch-context-<?=$site ?>" 
                        data-init="<?=$targetWitch->context?>">
                    <option value="">Empty</option>
                    
                    <?php /*foreach( $website->listContexts() as $contextItem ): ?>
                        <option value="<?=$contextItem ?>"><?=$contextItem ?></option>
                    <?php endforeach; */?>
                </select-->                
            </div>
        <?php endforeach; ?>
        
        <div id="site-selected"
             <?=!$targetWitch->site? 'style="display: none;"' :'' ?>>
            <div class="auto-url-disabled"
                 <?=!$targetWitch->url? 'style="display: none;"' :'' ?>>
                <label for="witch-url">
                    URL
                </label>
                <div class="url-input">
                    <span class="url-input-prefix">/</span>
                    <input  type="text"
                            name="witch-url"
                            id="witch-url"
                            data-init="<?=$targetWitch->url?>"
                            value="<?=$targetWitch->url ?>" />
                </div>
                <label  title="uncheck if you want to input a closest URL parent relative URL"
                        for="witch-full-url">Full URL</label>
                <input  title="uncheck if you want to input a closest URL parent relative URL"
                        type="checkbox" 
                        id="witch-full-url" 
                        <?=$targetWitch->url ? 'checked': '' ?>
                        name="witch-full-url" />
            </div>
            
            <label for="witch-auto-url">
                Automatic URL generation
            </label>
            <input type="checkbox" 
                   id="witch-auto-url" 
                   <?=$targetWitch->url ? '': 'checked' ?>
                   name="witch-automatic-url" />
        </div>
    </form>
    
    <div class="box__actions">
        <button class="trigger-action" 
                data-target="edit-witch-info"
                data-action="save-witch-info">
            <i class="fas fa-save"></i>
            Save
        </button>        
        <button class="edit-info-reinit">
            <i class="fa fa-undo"></i>
            Reinit Form
        </button>        
        <button class="view-edit-info-toggle">
            <i class="fa fa-times"></i>
            Cancel
        </button>
    </div>
</div>
