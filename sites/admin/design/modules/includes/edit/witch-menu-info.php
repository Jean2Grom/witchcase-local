<div class="box edit__witch-menu-info">
    <form   method="post"
            action="<?=$this->wc->website->getUrl('edit?id='.$targetWitch->id) ?>"
            id="edit-witch-menu-info">
        
        <p class="alert-message error" style="display: none;">Mandatory field</p>
        <input  type="text" 
                value="<?=$targetWitch->name ?>" 
                data-init="<?=$targetWitch->name ?>" 
                name="witch-name" 
                id="witch-name" />
        
        <label for="witch-data">Description</label>
        <textarea   name="witch-data" 
                    id="witch-data" 
                    data-init="<?=$targetWitch->data ?>"><?=$targetWitch->data ?></textarea>
    </form>
    
    <div class="box__actions">
        <button class="trigger-action" 
                data-target="edit-witch-menu-info"
                data-action="save-witch-menu-info">
            <i class="fas fa-save"></i>
            Save
        </button>
        
        <button class="edit-menu-info-reinit">
            <i class="fa fa-undo"></i>
            Reinit Form
        </button>        
        <button class="view-edit-menu-info-toggle">
            <i class="fa fa-times"></i>
            Cancel
        </button>
    </div>
</div>