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

<table class="cells">
    <tr>
        <td class="coltop"></td>
        <td class="coltop">#</td>
        <td class="coltop">{PHP.L.Category}</td>
        <td class="coltop">{PHP.L.ba_client}</td>
		<td class="coltop">{PHP.L.ba_query}</td>
        <td class="coltop">{PHP.L.Edit}</td>
        <td class="coltop">{PHP.L.Delete}</td>
        <td class="coltop">ID</td>
    </tr>
    <!-- BEGIN: LIST_ROW -->
    <tr>
        <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_NUM}</td>
        <td class="{LIST_ROW_ODDEVEN}">{LIST_ROW_CAT}</td>
        <td class="{LIST_ROW_ODDEVEN}">{LIST_ROW_CLIENT}</td>
        <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_STRING}</td>
        <td class="{LIST_ROW_ODDEVEN} centerall">
            <a href="{LIST_ROW_URL}"><img src="images/icons/default/arrow-follow.png" /></a>
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

<a href="{PHP|cot_url('admin', 'm=other&p=banners&n=queries&a=edit')}" class="button">{PHP.L.Add}</a>
<!-- END: MAIN -->