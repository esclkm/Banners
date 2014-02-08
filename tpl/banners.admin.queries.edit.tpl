<!-- BEGIN: MAIN -->
<div class="quick-actions">

    <a href="{PHP|cot_url('admin', 'm=other&p=banners')}" class="quick-action icon ticket">{PHP.L.ba_banners}</a>
    <a href="{PHP|cot_url('admin', 'm=structure&n=banners')}" class="quick-action icon folder">{PHP.L.Categories}</a>
    <a href="{PHP|cot_url('admin', 'm=other&p=banners&n=clients')}" class="quick-action icon vcard">{PHP.L.ba_clients}</a>
    <a href="{PHP|cot_url('admin', 'm=other&p=banners&n=track')}" class="quick-action icon chart-line">{PHP.L.ba_tracks}</a>
	<a href="{PHP|cot_url('admin', 'm=other&p=banners&n=queries')}" class="quick-action icon target">{PHP.L.ba_queries}</a>
    <a href="{PHP.db_banners|cot_url('admin', 'm=extrafields&n=$this')}" class="quick-action icon database">{PHP.L.adm_extrafields_table} {PHP.db_ba_banners}</a>
    <div class="clear height0"></div>
</div>


{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}

<!-- IF {PAGE_TITLE} -->
<h2 class="tags">{PAGE_TITLE}</h2>
<!-- ENDIF -->

<!-- BEGIN: FORM -->
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

    <input type="submit" value="{PHP.L.Submit}" />

    <!-- IF {FORM_ID} > 0 -->
    <a href="{FORM_DELETE_URL}" class="confirmLink button"><img src="images/icons/default/delete.png" style="vertical-align: middle;" />
    {PHP.L.Delete}</a>
    <!-- ENDIF -->
</form>
<!-- END: FORM -->


<!-- END: MAIN -->