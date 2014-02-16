<!-- BEGIN: MAIN -->
{FILE "{PHP.cfg.plugins_dir}/banners/tpl/banners.admin.headlinks.tpl"}

{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}

<div class="block">
    <h3>{PAGE_TITLE}</h3>
    <table class="cells">
        <tr>
            <td class="coltop"></td>
            <td class="coltop">{PHP.L.Title}</td>
            <td class="coltop">{PHP.L.ba_purchase_type}</td>
            <td class="coltop">{PHP.L.ba_published}</td>
            <td class="coltop">{PHP.L.Action}</td>
        </tr>
        <!-- BEGIN: LIST_ROW -->
        <tr>
            <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_NUM}</td>
            <td class="{LIST_ROW_ODDEVEN}"><a href="{LIST_ROW_URL}">{LIST_ROW_TITLE}</a></td>
            <td class="{LIST_ROW_ODDEVEN}">{LIST_ROW_PURCHASE_TEXT}</td>
            <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_PUBLISHED}</td>
            <td class="{LIST_ROW_ODDEVEN}">
                <a href="{LIST_ROW_ID|cot_url('admin', 'm=other&p=banners&n=clients&a=edit&id=$this')}" class="ajax button list icon">{PHP.L.Edit}</a>
                <a href="{LIST_ROW_DELETE_URL}" class="confirmLink negative button trash icon">{PHP.L.short_delete}</a>
            </td>
        </tr>
        <!-- END: LIST_ROW -->

        <!-- IF {LIST_TOTALLINES} == '0' -->
        <tr>
            <td class="odd centerall" colspan="5">{PHP.L.None}</td>
        </tr>
        <!-- ENDIF -->

    </table>
    <div class="action_bar valid">
    <!-- IF {LIST_CURRENTPAGE} -->
        <p class="paging">{LIST_PAGEPREV}{LIST_PAGINATION}{LIST_PAGENEXT} <span>{PHP.L.Total}: {LIST_TOTALLINES}, {PHP.L.Onpage}: {LIST_ITEMS_ON_PAGE}</span></p>
    <!-- ENDIF -->
        <a href="{PHP|cot_url('admin', 'm=other&p=banners&n=clients&a=edit')}" class="button confirm">{PHP.L.Add}</a>
    </div>
</div>

<!-- END: MAIN -->