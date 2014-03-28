<!-- BEGIN: MAIN -->

{FILE "{PHP.cfg.plugins_dir}/banners/tpl/banners.admin.headlinks.tpl"}
{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}

<div class="block">
	<h3 class="tags">{PAGE_TITLE}</h3>
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
			<!-- BEGIN: EXTRAFLD -->
			<tr>
				<td>{FORM_EXTRAFLD_TITLE}:</td>
				<td>{FORM_EXTRAFLD}</td>
			</tr>
			<!-- END: EXTRAFLD -->
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
		<div class="action_bar valid">
			<button type="submit" class="submit">{PHP.L.Submit}</button>
			<a href="{FORM_DELETE_URL}" class="confirmLink negative button">{PHP.L.Delete}</a>
		</div>

	</form>
</div>
	<!-- BEGIN: STAT -->
	<div class="quick-actions">

		<a href="{LIST_URL_IMPRESSIONS}" class="quick-action icon eye">{PHP.L.ba_impressions}</a>
		<a href="{LIST_URL_CLICKS}" class="quick-action icon target">{PHP.L.ba_clicks}</a>
		<a href="{LIST_URL_DAY}" class="quick-action icon book">{PHP.L.ba_day}</a>
		<a href="{LIST_URL_MONTH}" class="quick-action icon book">{PHP.L.ba_month}</a>
		<a href="{LIST_URL_YEAR}" class="quick-action icon book">{PHP.L.ba_year}</a>
		<div class="clear height0"></div>
	</div>


	<div class="block">
		<h3>{PHP.L.Statistics}:</h3>
		<table class="cells margintop10">
			<tr>
				<td class="coltop">{PHP.L.Date}</td>
				<td class="coltop">{PHP.L.Type}</td>
				<td class="coltop">{PHP.L.Count}</td>
			</tr>
			<!-- BEGIN: LIST_ROW -->
			<tr>
				<td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_TRACK_DATE}</td>
				<td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_TRACK_TYPE_TEXT}</td>
				<td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_TRACK_COUNT}</td>

			</tr>
			<!-- END: LIST_ROW -->

			<!-- IF {LIST_TOTALLINES} == '0' -->
			<tr>
				<td class="odd centerall" colspan="3">{PHP.L.None}</td>
			</tr>
			<!-- ENDIF -->
			</table>
	</div>
	<!-- END: STAT -->
<!-- END: MAIN -->