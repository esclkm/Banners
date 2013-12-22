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

<table class="cells">
    <tr>
        <td class="coltop"></td>
        <td class="coltop">{PHP.L.Title}</td>
        <td class="coltop">{PHP.L.ba_purchase_type}</td>
        <td class="coltop">{PHP.L.ba_published}</td>
        <td class="coltop">{PHP.L.Edit}</td>
        <td class="coltop">{PHP.L.Delete}</td>
        <td class="coltop">ID</td>
    </tr>
    <!-- BEGIN: LIST_ROW -->
    <tr>
        <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_NUM}</td>
        <td class="{LIST_ROW_ODDEVEN}"><a href="{LIST_ROW_URL}">{LIST_ROW_TITLE}</a></td>
        <td class="{LIST_ROW_ODDEVEN}">{LIST_ROW_PURCHASE_TEXT}</td>
        <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_PUBLISHED}</td>
        <td class="{LIST_ROW_ODDEVEN} centerall">
            <a href="{LIST_ROW_ID|cot_url('admin', 'm=other&p=banners&n=clients&a=edit&id=$this')}"><img src="images/icons/default/arrow-follow.png" /></a>
        </td>
        <td class="{LIST_ROW_ODDEVEN} centerall">
            <a href="{LIST_ROW_DELETE_URL}" class="confirmLink"><img src="images/icons/default/delete.png" /></a>
        </td>
        <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_ID}</td>
    </tr>
    <!-- END: LIST_ROW -->

    <!-- IF {LIST_TOTALLINES} == '0' -->
    <tr>
        <td class="odd centerall" colspan="12">{PHP.L.None}</td>
    </tr>
    <!-- ENDIF -->

</table>

<!-- IF {LIST_CURRENTPAGE} -->
<div class="paging">
    {LIST_PAGEPREV}{LIST_PAGINATION}{LIST_PAGENEXT}<span>{PHP.L.Total}: {LIST_TOTALLINES},
        {PHP.L.Onpage}: {LIST_ITEMS_ON_PAGE}</span>
</div>
<!-- ENDIF -->

<a href="{PHP|cot_url('admin', 'm=other&p=banners&n=clients&a=edit')}" class="button">{PHP.L.Add}</a>
<!-- END: MAIN -->