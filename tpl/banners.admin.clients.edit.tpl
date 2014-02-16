<!-- BEGIN: MAIN -->
{FILE "{PHP.cfg.plugins_dir}/banners/tpl/banners.admin.headlinks.tpl"}

{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}

<div class="block">
<h3>{PAGE_TITLE}</h3>

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
        <div class="action_bar valid">
            <input type="submit" class="button confirm" value="{PHP.L.Submit}" />
            <!-- IF {FORM_ID} > 0 --><a href="{FORM_DELETE_URL}" class="confirmLink button negative">{PHP.L.Delete}</a><!-- ENDIF -->
        </div>
    </form>
</div>

<!-- END: MAIN -->