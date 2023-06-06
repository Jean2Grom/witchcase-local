<?php 
    $this->addJsFile('triggers.js');
?>
<div class="box edit__invoke">
    <form   method="post"
            action="<?=$this->wc->website->getUrl('edit?id='.$targetWitch->id) ?>"
            id="edit-witch-invoke">
        <h3>Edit Witch Invoke Form</h3>
        
        <label for="witch-site">Site</label>
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
                <label for="witch-invoke-<?=$site ?>">Module to invoke</label>
                <select name="witch-invoke[<?=$site ?>]" 
                        id="witch-invoke-<?=$site ?>"                                           
                        data-init="<?=$targetWitch->invoke ?>">
                    <option value="">No module</option>
                    <?php foreach( $website->listModules() as $moduleItem ): ?>
                        <option <?=($targetWitch->invoke === $moduleItem)? 'selected': '' ?>
                                value="<?=$moduleItem ?>"><?=$moduleItem ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label for="witch-status-<?=$site ?>">Status limitation</label>
                <select name="witch-status[<?=$site ?>]" 
                        id="witch-status-<?=$site ?>"  
                        data-init="<?=$targetWitch->statusLevel ?>">
                    <?php foreach(  $website->get('status') as $statusKey => $statusLabel ): ?>
                        <option <?=($targetWitch->statusLevel === $statusKey)? 'selected': '' ?>
                                value="<?=$statusKey ?>"><?=$statusLabel ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label for="witch-context-<?=$site ?>">Forced Context</label>
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
                <label for="witch-url">URL</label>
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
            
            <label for="witch-auto-url">Auto URL</label>
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
        <button class="view-edit-invoke-toggle">Cancel</button>
    </div>
</div>

<script>
$(document).ready(function()
{
    $('#witch-site').change( witchSiteChange );
    $('#witch-auto-url').change( autoUrlChange );
    
    function witchSiteChange()
    {    
        $('.witch-invoke__part').hide();
        $('.site-selected').hide();
        
        let site = $('#witch-site').val();
        if( site !== '' )
        {
            $('.witch-invoke__part-' + site).show();
            $('.site-selected').show();            
        }
    }
    
    function autoUrlChange()
    {
        if( $('#witch-auto-url').prop('checked') ){
            $('.auto-url-disabled').hide();
        }
        else {
            $('.auto-url-disabled').show();
        }
    }
    
    $('button.edit-invoke-reinit').click(function(){
        $('.edit__invoke input, .edit__invoke select').each(function( i, input ){

            if( $(input).data('init') !== undefined ){
                $(input).val( $(input).data('init') );
            }
        });
        
        if( $('#witch-url').val() === '' )
        {
            $('#witch-full-url').prop('checked', false);
            $('#witch-auto-url').prop('checked', true);
        }
        else 
        {
            $('#witch-full-url').prop('checked', true);
            $('#witch-auto-url').prop('checked', false);
        }
        witchSiteChange();
        autoUrlChange();
    });
});
</script> 