<form action="{$VAL_SELF}" method="post" enctype="multipart/form-data">
	<div id="TaxExempt" class="tab_content">
  		<h3>{$TITLE}</h3>
  		<p>{$LANG.tax_exempt.module_description}</p>
  		<fieldset><legend>{$LANG.module.cubecart_settings}</legend>
			<div><label for="status">{$LANG.common.status}</label><span><input type="hidden" name="module[status]" id="status" class="toggle" value="{$MODULE.status}" /></span></div>
			<input type="hidden" name="module[db_install]" id="db_install" value="{$MODULE.db_install}" />
			<div><label for="db_uninstall">{$LANG.tax_exempt.title_db_uninstall}*</label><span><input type="hidden" name="tax_exempt_uninstall" id="db_uninstall" class="toggle" value="" /></span></div>
			<p>* {$LANG.tax_exempt.db_uninstall_instructions}</p>
			<p>{$LANG.tax_exempt.db_modifications}</p>
		</fieldset>
		<fieldset>
			<legend>{$LANG.tax_exempt.title_db_modifications}</legend>
			<table width="100%">
				<head>
					<tr>
						<td><strong>{$LANG.tax_exempt.title_db_table}</strong></td>
						<td><strong>{$LANG.tax_exempt.title_db_column}</strong></td>
						<td><strong>{$LANG.tax_exempt.title_db_definition}</strong></td>
						<td><strong>{$LANG.tax_exempt.title_db_notes}</strong></td>
					</tr>
				</head><tbody>
					{foreach from=$TAXEXEMPT_DB item=db}
					<tr>
						<td>{$db.table}</td>
						<td>{$db.column}</td>
						<td>{$db.definition}</td>
						<td>{$db.comment}</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
		</fieldset>
		<h3>{$LANG.tax_exempt.enabled_groups}</h3>
		<fieldset id="enabled-groups"><legend>{$LANG.tax_exempt.title_groups_enabled}</legend>
		{if isset($ENABLED_GROUPS)}
		{foreach from=$ENABLED_GROUPS item=group}
			<div>
				<span class="actions"><a href="#" class="remove dynamic" title="{$LANG.messages.confirm_delete}"><i class="fa fa-trash" title="{$LANG.common.delete}"></i></a></span>
				<input type="hidden" name="module[groups][]" value="{$group.group_id}">
				{$group.group_name}
			</div>
		{/foreach}
		{/if}
		</fieldset>
		{if isset($CUSTOMER_GROUPS)}
		<fieldset><legend>{$LANG.tax_exempt.title_groups_add}</legend>
		<div class="inline-add">
			<label for="add-group">{$LANG.tax_exempt.groups_add}</label>
			<span>
				<select id="add-group" name="module[groups][]" class="textbox add display">
					<option value="">{$LANG.form.please_select}</option>
					{foreach from=$CUSTOMER_GROUPS item=group}
						<option value="{$group.group_id}">{$group.group_name}</option>
					{/foreach}
				</select>
				<a href="#" class="add" target="enabled-groups"><i class="fa fa-plus-circle" title="{$LANG.common.add}"></i></a>
			</span>
		</div>
		</fieldset>
		{/if}
	</div>
	{$MODULE_ZONES}
	<div class="form_control">
		<input type="submit" name="save" value="{$LANG.common.save}" />
	</div>
  	<input type="hidden" name="token" value="{$SESSION_TOKEN}" />
</form>