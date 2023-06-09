<div class="box edit__info">
    <form   method="post"
            action="<?=$this->wc->website->getUrl('edit?id='.$targetWitch->id) ?>"
            id="edit-witch-info">
        <h3>Edit Witch Info Form</h3>
        
        <p class="alert-message error" style="display: none;">Mandatory field</p>
        <label for="witch-name">Name*</label>
        <input  type="text" 
                value="<?=$targetWitch->name ?>" 
                data-init="<?=$targetWitch->name ?>" 
                name="witch-name" 
                id="witch-name" />
        
        <label for="witch-data">Description</label>
        <textarea   name="witch-data" 
                    id="witch-data" 
                    data-init="<?=$targetWitch->data ?>"><?=$targetWitch->data ?></textarea>
        
        <label for="witch-priority">Priority</label>
        <input  type="number" 
                value="<?=$targetWitch->priority ?>" 
                data-init="<?=$targetWitch->priority ?>" 
                name="witch-priority" 
                id="witch-priority" />
    </form>
    
    <div class="box__actions">
        <button class="trigger-action" 
                data-target="edit-witch-info"
                data-action="save-witch-info">
            Save
        </button>
        
        <button class="edit-info-reinit">Reinit Form</button>        
        <button class="view-edit-info-toggle">Cancel</button>
    </div>
</div>