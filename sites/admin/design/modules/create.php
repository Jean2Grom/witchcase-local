<?php /** @var WC\Module $this */

$this->addCssFile('view.css');
$this->addCssFile('boxes.css');
$this->addJsFile('triggers.js');

$this->addContextArrayItems( 'tabs', [
    'tab-current'       => [
        'selected'  => true,
        'iconClass' => "fa fa-plus",
        'text'      => "Add Witch a Daughter",
    ],
]);
?>

<?php $this->include('alerts.php', ['alerts' => $this->wc->user->getAlerts()]); ?>

<div class="view__witch-menu-info">
    <h2 title="<?=$this->witch->data ?>">
        <?=$targetWitch->name ?>
    </h2>
    <p><em><?=$targetWitch->data ?></em></p>    
</div>

<div class="tabs-target__item selected"  id="tab-current">
    <form method="post" id="create-witch">
        <div class="box-container">
            <div class="box ">
                <h3 class="box-info">
                    <img src="<?=$this->image('favicon.png') ?>" />
                    Witch
                </h3>
                <label for="new-witch-name">Name*</label>
                <input  type="text" 
                        value="" 
                        placeholder="new witch name"
                        name="new-witch-name" 
                        id="new-witch-name" />                
                <label for="new-witch-data">Description</label>
                <textarea   name="new-witch-data" 
                            id="new-witch-data" 
                            placeholder="new witch short description"></textarea>
                
                <label for="new-witch-priority">Priority</label>
                <input  type="number" 
                        value="" 
                        name="new-witch-priority" 
                        id="new-witch-priority" />
            </div>

            <div class="box ">
                <h3>
                    <i class="fas fa-hand-sparkles"></i>
                    Access
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
                
                <label for="witch-status">
                    Status
                </label>
                <select name="witch-status" 
                        id="witch-status" 
                        data-init="<?=$targetWitch->statusLevel ?>"></select>
                
                <div id="site-selected"
                    <?=!$targetWitch->site? 'style="display: none;"' :'' ?>>
                    
                    <label for="witch-invoke">
                        Module to invoke
                    </label>
                    <select name="witch-invoke" 
                            id="witch-invoke"
                            data-init="<?=$targetWitch->invoke ?>"></select>
                    
                    <div    id="invoke-selected"
                            <?=!$targetWitch->invoke? 'style="display: none;"' :'' ?>>

                        <div id="auto-url-disabled" style="display: none;">
                            <label for="witch-url">
                                URL
                            </label>
                            <div class="url-input">
                                <span class="url-input-prefix">/</span>
                                <input  type="text"
                                        name="witch-url"
                                        id="witch-url"
                                        value="" />
                            </div>
                            <label  title="uncheck if you want to input a closest URL parent relative URL"
                                    for="witch-full-url">Full URL</label>
                            <input  title="uncheck if you want to input a closest URL parent relative URL"
                                    type="checkbox" 
                                    id="witch-full-url" 
                                    name="witch-full-url" />
                        </div>
                        
                        <label for="witch-auto-url">
                            Automatic URL generation
                        </label>
                        <input type="checkbox" 
                            id="witch-auto-url" 
                            checked="true"
                            name="witch-automatic-url" />
                    </div>
                </div>

            </div>
        
            <div class="box">
                <h3>
                    <i class="fa fa-mortar-pestle"></i>
                    Cauldron
                </h3>
                
                <label for="witch-cauldron-structure">
                    New cauldron structure
                </label>
                <select name="witch-cauldron-structure" id="witch-cauldron-structure">
                    <option value="">
                        No cauldron creation
                    </option>
                    <?php foreach( $this->wc->configuration->structures() as $structure ): ?>
                        <option value="<?=$structure->name?>">
                            <?=$structure->name?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <input type="hidden" id="imported-cauldron-witch" name="imported-cauldron-witch" value="" />
                
                <div class="box__actions">
                    <button id="import-cauldron-action" 
                            class="trigger-action"
                            style="display: none;"
                            data-action="import-cauldron"
                            data-target="witch-add-new-cauldron">Import craft</button>
                    <button id="witch-get-existing-cauldron">
                        <i class="fa fa-project-diagram"></i>
                        Get existing cauldron
                    </button>
                </div>
            </div>
        </div>
    </form>

    <button class="trigger-action" 
            data-target="create-witch"
            data-action="create-new-witch">
        <i class="fa fa-plus"></i>
        Create
    </button>
    
    <button class="trigger-href" 
            data-href="<?=$cancelHref ?>">
        <i class="fa fa-times"></i>
        Cancel
    </button>
</div>



<script type="text/javascript">
document.addEventListener("DOMContentLoaded", () => 
{
    var status  = <?=json_encode( $status, JSON_FORCE_OBJECT )?>; 
    var modules = <?=json_encode( $modules )?>; 
    
    setSiteImpactedInputs();
    setInvokeImpactedInputs();
    setAutoUrlImpactedInputs();

    document.getElementById("witch-site").addEventListener('change', () => setSiteImpactedInputs());
    document.getElementById("witch-invoke").addEventListener('change', () => setInvokeImpactedInputs());
    document.getElementById("witch-auto-url").addEventListener('change', () => setAutoUrlImpactedInputs());

    function setSiteImpactedInputs()
    {
        let site = document.getElementById('witch-site').value;

        let statusDom       = document.getElementById('witch-status');
        statusDom.innerHTML = "";

        let siteStatus = status[ site ];
        if( siteStatus === undefined ){
            siteStatus =  status['global'];
        }

        Object.keys( siteStatus ).forEach( value => 
        {
            let option = document.createElement('option');
            option.setAttribute( 'value', value );
            option.append( siteStatus[value] );

            if( statusDom.dataset.init === value ){
                option.setAttribute('selected', true);
            }

            statusDom.append( option );
        });
        
        if( site === '' ){
            document.getElementById('site-selected').style.display = 'none';
        }
        else {
            document.getElementById('site-selected').style.display = 'block';
        }

        let modulesDom          = document.getElementById('witch-invoke');
        modulesDom.innerHTML    = "";

        let emptyOption = document.createElement('option');
        emptyOption.setAttribute('value', '');
        emptyOption.append("no invoke");

        modulesDom.append( emptyOption );

        let siteModules = modules[ site ];
        if( siteModules === undefined ){
            siteModules =  [];
        }

        siteModules.forEach( value => 
        {
            let option = document.createElement('option');
            option.setAttribute( 'value', value );
            option.append( value );

            if( modulesDom.dataset.init === value ){
                option.setAttribute('selected', true);
            }

            modulesDom.append( option );
        });

        return;
    }

    function setInvokeImpactedInputs()
    {
        let invoke = document.getElementById('witch-invoke').value;

        if( invoke === '' ){
            document.getElementById('invoke-selected').style.display = 'none';
        }
        else {
            document.getElementById('invoke-selected').style.display = 'block';
        }

        return;
    }

    function setAutoUrlImpactedInputs()
    {
        if( document.getElementById('witch-auto-url').checked ){
            document.getElementById('auto-url-disabled').style.display = 'none';
        }
        else {
            document.getElementById('auto-url-disabled').style.display = 'block';
        }

        return;
    }

    // Cauldron part
    function resetCauldronInputs()
    {
        document.querySelectorAll('.importVisualizeDom').forEach( dom => dom.remove() );
        document.getElementById('witch-cauldron-structure').value   = '';
        document.getElementById('imported-cauldron-witch').value    = '';
        document.getElementById('witch-cauldron-structure').removeAttribute('disabled');
    }

    let cauldronFetchButton = document.getElementById('witch-get-existing-cauldron');
    if( cauldronFetchButton ){
        cauldronFetchButton.addEventListener('click', e => 
        {
            e.preventDefault();

            chooseWitch({ cauldron: true }, "Choose importing cauldron's witch")
            .then( witchId => 
            { 
                if( witchId === false ){
                    return false;
                }

                resetCauldronInputs();

                document.getElementById('witch-cauldron-structure').setAttribute('disabled', true); 

                let importVisualizeDom = document.createElement('div');
                importVisualizeDom.classList.add('importVisualizeDom');

                let span = document.createElement('span');
                span.append( "Import from : " + readWitchName(witchId) );

                let closeIcon = document.createElement('i');
                closeIcon.classList.add('fa');
                closeIcon.classList.add('fa-times');
                closeIcon.style['margin-left'] = '8px';
                let closeLink = document.createElement('a');
                closeLink.append( closeIcon );

                importVisualizeDom.append( span );
                importVisualizeDom.append( closeLink );
                cauldronFetchButton.closest('.box').append( importVisualizeDom );

                closeLink.addEventListener('click', () => resetCauldronInputs());
                document.getElementById('imported-cauldron-witch').value = witchId;

                return false;
            });

            return false;
        });    
    }

    let cauldronPositionButton = document.getElementById('choose-cauldron-position');
    if( cauldronPositionButton ){
        cauldronPositionButton.addEventListener('click', () => 
            chooseWitch({ cauldron: true }, "Choose new cauldron position")
            .then( witchId => 
            {
                if( witchId === false ){
                    return;
                }

                document.getElementById('imported-cauldron-witch').value = witchId;
                document.getElementById('import-cauldron-action').click();
            })
        );
    }
});
</script>
