<!-- BEGIN: MAIN -->
<div class="block button-toolbar">
    <a href="{PHP|cot_url('admin', 'm=other&p=banners')}" class="button">{PHP.L.ba_banners}</a>
    <a href="{PHP|cot_url('admin', 'm=structure&n=banners')}" class="button">{PHP.L.Categories}</a>
    <a href="{PHP|cot_url('admin', 'm=other&p=banners&n=clients')}" class="button">{PHP.L.ba_clients}</a>
    <a href="{PHP|cot_url('admin', 'm=other&p=banners&n=track')}" class="button">{PHP.L.ba_tracks}</a>
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