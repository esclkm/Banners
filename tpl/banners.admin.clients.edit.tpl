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
<form action="{FORM_ID|cot_url('admin', 'm=other&p=banners&n=clients&a=edit&id=$this')}" method="POST">
    <input type="hidden" name="act" value="save" />

    <table class="cells">
        <tr>
            <td class="width20">{PHP.L.Title}:</td>
            <td>{FORM_TITLE}</td>
        </tr>
        <tr>
            <td>{PHP.L.Email}:</td>
            <td>{FORM_EMAIL}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_purchase_type}:</td>
            <td>{FORM_PURCHASE_TYPE}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_track_impressions}:</td>
            <td>{FORM_TRACK_IMP} <br />{PHP.L.ba_track_impressions_tip}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_track_clicks}:</td>
            <td>{FORM_TRACK_CLICKS} <br />{PHP.L.ba_track_clicks_tip}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_extrainfo}:</td>
            <td>{FORM_EXTRAINFO}</td>
        </tr>
        <!-- BEGIN: EXTRAFLD -->
        <tr>
            <td>{FORM_EXTRAFLD_TITLE}:</td>
            <td>{FORM_EXTRAFLD}</td>
        </tr>
        <!-- END: EXTRAFLD -->
        <tr>
            <td>{PHP.L.ba_published}?:</td>
            <td>{FORM_PUBLISHED}</td>
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