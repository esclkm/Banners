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
<form action="{FORM_ID|cot_url('admin', 'm=other&p=banners&a=edit&id=$this')}" method="POST" ENCTYPE="multipart/form-data">
   <!-- <input type="hidden" name="rid" value="{FORM_ID}" /> -->
    <input type="hidden" name="act" value="save" />

    <table class="cells">
        <tr>
            <td class="width20">{PHP.L.Title}:</td>
            <td>{FORM_TITLE}</td>
        </tr>
        <tr>
            <td>{PHP.L.Category}:</td>
            <td>{FORM_CATEGORY}</td>
        </tr>
        <tr>
            <td>{PHP.L.Type}:</td>
            <td>{FORM_TYPE}</td>
        </tr>
        <tr>
            <td>{PHP.L.Image}:</td>
            <td>
                <div class="admin-banner-preview marginbottom10">{BANNER_IMAGE}</div>
                {FORM_FILE}
            </td>
        </tr>
        <tr>
            <td>{PHP.L.ba_width}:</td>
            <td>{FORM_WIDTH} {PHP.L.ba_for_file_only}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_height}:</td>
            <td>{FORM_HEIGHT} {PHP.L.ba_for_file_only}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_alt}:</td>
            <td>{FORM_ALT}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_custom_code}:</td>
            <td>{FORM_CUSTOMCODE}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_click_url}:</td>
            <td>{FORM_CLICKURL}</td>
        </tr>
        <tr>
            <td>{PHP.L.Description}:</td>
            <td>{FORM_DESCRIPTION}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_sticky}?:</td>
            <td>{FORM_STICKY}<br />{PHP.L.ba_sticky_tip}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_begin}:</td>
            <td>{FORM_BEGIN}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_expire}:</td>
            <td>{FORM_PUBLISH_DOWN}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_imptotal}:</td>
            <td>{FORM_IMPTOTAL} 0 - {PHP.L.ba_unlimited}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_impmade}:</td>
            <td>{FORM_IMPMADE}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_clicks_all}:</td>
            <td>{FORM_CLICKS}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_client}:</td>
            <td>{FORM_CLIENT_ID}</td>
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