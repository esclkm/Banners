
<div class="quick-actions">

    <a href="{PHP|cot_url('admin', 'm=other&p=banners')}" class="quick-action icon ticket">{PHP.L.ba_banners}</a>
    <a href="{PHP|cot_url('admin', 'm=structure&n=banners')}" class="quick-action icon folder">{PHP.L.Categories}</a>
    <a href="{PHP|cot_url('admin', 'm=other&p=banners&n=clients')}" class="quick-action icon vcard">{PHP.L.ba_clients}</a>
    <a href="{PHP|cot_url('admin', 'm=other&p=banners&n=track')}" class="quick-action icon chart-line">{PHP.L.ba_tracks}</a>
	<a href="{PHP|cot_url('admin', 'm=other&p=banners&n=queries')}" class="quick-action icon target">{PHP.L.ba_queries}</a>
    <a href="{PHP.db_banners|cot_url('admin', 'm=extrafields&n=$this')}" class="quick-action icon database">{PHP.L.adm_extrafields_table} {PHP.db_ba_banners}</a>
    <div class="clear height0"></div>
</div>
