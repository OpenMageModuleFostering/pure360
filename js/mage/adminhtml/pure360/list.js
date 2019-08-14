Event.observe(window, 'load', function() {
	initPure360List();
});

Pure360List = Class.create();

Pure360List.prototype =
		{
			initialize: function(scope, scopeId, saveListUrl, removeListUrl, resyncListUrl) {
				this.scope = scope;
				this.scope_filter = (this.scope === 'default' ? 'default_' : this.scope + '_');
				this.scopeId = scopeId;
				this.saveListUrl = saveListUrl;
				this.removeListUrl = removeListUrl;
				this.resyncListUrl = resyncListUrl;

				this.enabled = document.getElementById('pure360_list_' + this.scope_filter + 'settings_enabled');

				if (this.enabled)
				{
					this.enabledDefault = this.enabled.value;
					this.listId = document.getElementById('pure360_list_id');
					this.trackingEnabled = document.getElementById('pure360_list_success_tracking_enabled');
					this.listRemove = document.getElementById('pure360_list_remove');
					this.listResync = document.getElementById('pure360_list_resync');
					this.configEditForm = document.getElementById('config_edit_form');

					this.oldFormSubmit = this.configEditForm.submit;

					// Setup observers
					var self = this;

					this.enabled.onchange = function(e) {
						return self.toggleEnabled();
					};
					this.trackingEnabled.onchange = function(e) {
						return self.toggleSuccessTrackingOptions();
					};
					this.configEditForm.submit = function(e) {
						return self.submitForm();
					};

					if (this.listRemove)
					{
						this.listRemove.onclick = function(e) {
							return self.removeList();
						};
					}
					
					if (this.listResync)
					{
						this.listResync.onclick = function(e) {
							return self.resyncList();
						};
					}

					this.toggleListOptions();
					this.toggleSuccessTrackingOptions();
				}
			},
			translate: function(text) {
				try {
					if (Translator) {
						return Translator.translate(text);
					}
				}
				catch (e) {
				}
				return text;
			},
			submitForm: function()
			{
				if (this.enabled.value != this.enabledDefault)
				{
					this.oldFormSubmit.call(this.configEditForm);

				} else
				{
					if (this.configEditForm.validator != null)
					{
						if (!this.configEditForm.validator.options.onFormValidate())
						{
							return false;
						}
					}

					this.saveList();
				}
			},
			toggleEnabled: function() {

				if (this.enabled.value == 1)
				{
					$('pure360_list_container').show();

				} else
				{
					$('pure360_list_container').hide();
				}
			},
			toggleListOptions: function() {

				if (this.listId.value == 0)
				{
					this.listRemove.hide();
				} else
				{
					this.listRemove.show();
				}
			},
			toggleSuccessTrackingOptions: function() {
				if ($('pure360_list_success_tracking_enabled').checked) {
					$('pure360_list_success_tracking_token').enable();
					$('pure360_list_success_tracking_token_row').show();
				}
				else {
					$('pure360_list_success_tracking_token').disable();
					$('pure360_list_success_tracking_token_row').hide();
				}
			},
			saveList: function() {
				var response, result;

				var dataFields = new Array();
				$$("input:checkbox[name=pure360_list_data_fields[]]:checked").each(function(el)
				{
					dataFields.push($(el).value)
				});
				var addressFields = new Array();
				$$("input:checkbox[name=pure360_list_address_fields[]]:checked").each(function(el)
				{
					addressFields.push($(el).value)
				});
				var salesFields = new Array();
				$$("input:checkbox[name=pure360_list_sales_fields[]]:checked").each(function(el)
				{
					salesFields.push($(el).value)
				});

				$url = this.saveListUrl
						+ "?listId=" + $("pure360_list_id").value
						+ "&scope=" + this.scope
						+ "&scopeId=" + this.scopeId
						+ "&listName=" + $('pure360_list_name').value
						+ "&listFilter=" + $('pure360_list_filter').value
						+ "&successTrackingEnabled=" + ($('pure360_list_success_tracking_enabled').checked ? 'y' : 'n')
						+ "&successTrackingToken=" + $('pure360_list_success_tracking_token').value
						+ "&doubleOptinEnabled=" + ($('pure360_list_double_optin_enabled').checked ? 'y' : 'n')
						+ "&listDataFields=" + dataFields
						+ "&listAddressFields=" + addressFields
						+ "&listSalesFields=" + salesFields

				response = this.getHttp($url);

				result = JSON.parse(response);

				if (result === 'EXISTS' || result === 'DIFFERENT')
				{
					var question = (result === 'EXISTS') ? 
						"List found in Response. If you save, the list will be APPENDED." :
								"List found in Response but with different custom fields. If you save, the list will be REPLACED.";
					
					var answer = confirm(question + " Do you still want to save?")

					if (answer)
					{
						response = this.getHttp($url + "&replace=true");
					}
				}

				location.reload(true);
			},
			removeList: function() {
				var response, result;

				$url = this.removeListUrl
						+ "?listId=" + $("pure360_list_id").value
						+ "&scope=" + this.scope
						+ "&scopeId=" + this.scopeId;

				var answer = confirm("Removing this list will stop the integration from working. All contacts will be set back to unsynced. The list will not be removed from Response. Are you sure you want to remove this list?")

				if (answer)
				{
					response = this.getHttp($url);
					location.reload(true);
				}
			},
			resyncList: function() {
				var response, result;

				$url = this.resyncListUrl
						+ "?listId=" + $("pure360_list_id").value
						+ "&scope=" + this.scope
						+ "&scopeId=" + this.scopeId;

				var answer = confirm("Re-syncing this list will trigger a list replace in Response. All subscribers will be re-synced. Are you sure you want to re-sync this list?")

				if (answer)
				{
					response = this.getHttp($url);
					location.reload(true);
				}
			},
			alertSaveChanges: function() {
				alert(this.txtNotSavechanges);
			},
			getHttp: function(url) {
				var response;
				new Ajax.Request(
						url,
						{
							method: "get",
							onComplete: function(transport) {
								response = transport.responseText;
							},
							asynchronous: false
						});
				return response;
			},
			getInteger: function(number) {
				number = parseInt(number);
				if (isNaN(number))
					return 0;
				return number;
			}
		}
