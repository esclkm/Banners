<!-- BEGIN: MAIN -->
{FILE "{PHP.cfg.plugins_dir}/banners/tpl/banners.admin.headlinks.tpl"}

{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}
<div class="block">
    <h3>{PHP.L.Filters}:</h3>
    <form method="get" action="{LIST_URL}">
        <div class="inner margintop10 marginbottom10">
            {PHP.L.Title}: <input type="text" name="fil[title]" value="{FILTER_VALUES.title}" />
            {PHP.L.Category}: {FILTER_CATEGORY}
            {PHP.L.ba_client}: {FILTER_CLIENT}
            {PHP.L.Type}: {FILTER_TRACK_TYPE}<br />
            {PHP.L.Date} {PHP.L.ba_from}: {FILTER_DATE_FROM} {PHP.L.ba_to} {FILTER_DATE_TO}
            <div style="text-align: right">
                <input type="hidden" name="m" value="{PHP.m}">
                <input type="hidden" name="p" value="{PHP.p}">
                <input type="hidden" name="n" value="{PHP.n}">
                {PHP.L.adm_sort}: {SORT_BY} {SORT_WAY}
            </div>
        </div>
        <div class="action_bar valid">
            <input type="submit" value="{PHP.L.Show}" />
        </div>

    </form>
</div>

<div class="block">
    <h3>{PAGE_TITLE}:</h3>
    <table class="cells margintop10">
        <tr>
            <td class="coltop"></td>
            <td class="coltop">{PHP.L.Title}</td>
            <td class="coltop">{PHP.L.ba_client}</td>
            <td class="coltop">{PHP.L.Type}</td>
            <td class="coltop">{PHP.L.Count}</td>
            <td class="coltop">{PHP.L.Date}</td>
        </tr>
        <!-- BEGIN: LIST_ROW -->
        <tr>
            <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_NUM}</td>
            <td class="{LIST_ROW_ODDEVEN}">
                <a href="{LIST_ROW_EDIT_URL}">{LIST_ROW_TITLE}</a>
                <div class="desc">{LIST_ROW_CATEGORY_TITLE}</div>
            </td>
            <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_CLIENT_TITLE}</td>
            <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_TRACK_TYPE_TEXT}</td>
            <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_TRACK_COUNT}</td>
            <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_TRACK_DATE}</td>
        </tr>
        <!-- END: LIST_ROW -->

        <!-- IF {LIST_TOTALLINES} == '0' -->
        <tr>
            <td class="odd centerall" colspan="12">{PHP.L.None}</td>
        </tr>
        <!-- ENDIF -->

    </table>

     <div class="action_bar valid">
    <!-- IF {LIST_CURRENTPAGE} -->
        <p class="paging">{LIST_PAGEPREV}{LIST_PAGINATION}{LIST_PAGENEXT} <span>{PHP.L.Total}: {LIST_TOTALLINES}, {PHP.L.Onpage}: {LIST_ITEMS_ON_PAGE}</span></p>
    <!-- ENDIF -->
            <button id="clearStats" class="button negative" name="a" value="clear"
            onclick="return confirm('{PHP.L.ba_clear_tracks_param_confirm}')">{PHP.L.ba_clear_tracks_param}</button>
    </div>
</div>

<!-- END: MAIN -->
