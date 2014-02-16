<!-- BEGIN: MAIN -->
{FILE "{PHP.cfg.plugins_dir}/banners/tpl/banners.admin.headlinks.tpl"}

{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}

<div class="block">
    <h3><!-- IF {FORM_ID} > 0 -->{PHP.L.ba_query_edit} ({FORM_ID})<!-- ELSE -->{PHP.L.ba_query_new}<!-- ENDIF -->:</h3>

<form action="{FORM_ID|cot_url('admin', 'm=other&p=banners&n=queries&a=edit&id=$this')}" method="POST">
    <input type="hidden" name="act" value="save" />

    <table class="cells">
        <tr>
            <td class="width20">{PHP.L.Category}:</td>
            <td>{FORM_CAT}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_client}:</td>
            <td>{FORM_CLIENT}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_query}:</td>
            <td>{FORM_STRING}</td>
        </tr>
    </table>

    <div class="action_bar valid">
        <input type="submit" class="button confirm" value="{PHP.L.Submit}" />
        <!-- IF {FORM_ID} > 0 --><a href="{FORM_DELETE_URL}" class="button negative">{PHP.L.Delete}</a><!-- ENDIF -->
    </div>
   
</form>

</div>

<!-- END: MAIN -->