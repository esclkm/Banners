<!-- BEGIN: MAIN -->
{FILE "{PHP.cfg.plugins_dir}/banners/tpl/banners.admin.headlinks.tpl"}

{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}

<div class="block">
    <h3>{PHP.L.Filters}</h3>

    <form method="get" action="{LIST_URL}">
    <table class="cells">
        <tr>
            <td>{PHP.L.Title}</td>
            <td><input type="text" name="fil[title]" value="{FILTER_VALUES.title}" /></td>
            <td>{PHP.L.Category}</td>
            <td>{FILTER_CATEGORY}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_client}</td>
            <td>{FILTER_CLIENT}</td>
            <td>{PHP.L.ba_published}</td>
            <td>{FILTER_PUBLISHED}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td>{PHP.L.adm_sort}</td>
            <td>
                {SORT_BY} {SORT_WAY}
                <input type="hidden" name="m" value="{PHP.m}">
                <input type="hidden" name="p" value="{PHP.p}">
            </td>
        </tr>
    </table>
        <div class="action_bar valid">
            <button type="submit" class="submit">{PHP.L.Show}</button>
        </div>
    </form>
</div>

<div class="block">
    <h3>{PAGE_TITLE}</h3>
    <table class="cells margintop10">
        <tr>
            <td class="coltop"></td>
            <td class="coltop">{PHP.L.Title}</td>
            <td class="coltop">{PHP.L.Category}</td>
            <td class="coltop">{PHP.L.ba_sticky}</td>
            <td class="coltop">{PHP.L.ba_published}</td>
            <td class="coltop">{PHP.L.ba_client}</td>
            <td class="coltop">{PHP.L.ba_impressions}</td>
            <td class="coltop">{PHP.L.ba_clicks_all}</td>
            <td class="coltop">{PHP.L.Action}</td>
        </tr>
        <!-- BEGIN: LIST_ROW -->
        <tr>
            <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_NUM}</td>
            <td class="{LIST_ROW_ODDEVEN}"><a href="{LIST_ROW_EDIT_URL}">{LIST_ROW_TITLE}</a></td>
            <td class="{LIST_ROW_ODDEVEN}">{LIST_ROW_CATEGORY_TITLE}</td>
            <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_STICKY_TEXT}</td>
            <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_PUBLISHED}</td>
            <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_CLIENT_TITLE}</td>
            <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_IMPMADE} / {LIST_ROW_IMPTOTAL_TEXT}</td>
            <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_CLICKS} / {LIST_ROW_CLICKS_PERSENT}</td>
            <td class="{LIST_ROW_ODDEVEN}">
                <a href="{LIST_ROW_ID|cot_url('admin', 'm=other&p=banners&a=edit&id=$this')}" class="ajax button list icon">{PHP.L.Edit}</a>
                <a href="{LIST_ROW_DELETE_URL}" class="confirmLink negative button trash icon">{PHP.L.short_delete}</a>
            </td>
        </tr>
        <!-- END: LIST_ROW -->

        <!-- IF {LIST_TOTALLINES} == '0' -->
        <tr>
            <td class="odd centerall" colspan="9">{PHP.L.None}</td>
        </tr>
        <!-- ENDIF -->

    </table>
    <div class="action_bar valid">

        <p class="paging">{LIST_PAGEPREV}{LIST_PAGINATION}{LIST_PAGENEXT} <span>{PHP.L.Total}: {LIST_TOTALLINES}, {PHP.L.Onpage}: {LIST_ITEMS_ON_PAGE}</span></p>
        <a href="{PHP|cot_url('admin', 'm=other&p=banners&a=edit')}" class="button cofirm">{PHP.L.Add}</a>
    </div>
</div>

<!-- END: MAIN -->