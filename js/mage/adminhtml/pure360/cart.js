Event.observe(window, 'load', function() {
	initPure360Trigger();
});

Pure360Trigger = Class.create();

Pure360Trigger.prototype =
		{
			initialize: function(scope, scopeId, saveTriggerUrl, clearCartsUrl) {
				this.scope = scope;
				this.scope_filter = (this.scope === 'default' ? 'default_' : this.scope + '_');
				this.scopeId = scopeId;
		
				this.saveTriggerUrl = saveTriggerUrl;
				this.clearCartsUrl = clearCartsUrl;
				this.enabled = document.getElementById('pure360_cart_' + this.scope_filter + 'settings_enabled');

				if (this.enabled)
				{
					this.enabledDefault = this.enabled.value;
					this.configEditForm = document.getElementById('config_edit_form');
					this.oldFormSubmit = this.configEditForm.submit;

					// Setup observers
					var self = this;

					this.enabled.onchange = function(e) {
						return self.toggleEnabled();
					};
					
					this.configEditForm.submit = function(e) {
						return self.submitForm();
					};
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
				if (this.enabled.value !== this.enabledDefault)
				{
					this.oldFormSubmit.call(this.configEditForm);

				} else
				{
					if (this.configEditForm && this.configEditForm.validator)
					{
						if (!this.configEditForm.validator.options.onFormValidate())
						{
							return false;
						}
					}

					this.saveTriggers();
				}
			},
			toggleEnabled: function() {

				if (this.enabled.value === '1')
				{
					if ($('pure360_cart_' + this.scope_filter + 'trigger1'))
					{
						$('pure360_cart_' + this.scope_filter + 'trigger1').show();
						$('pure360_cart_' + this.scope_filter + 'trigger1').previous().previous().show();
					}
					if ($('pure360_cart_' + this.scope_filter + 'trigger2'))
					{
						$('pure360_cart_' + this.scope_filter + 'trigger2').show();
						$('pure360_cart_' + this.scope_filter + 'trigger2').previous().previous().show();
					}
					if ($('pure360_cart_' + this.scope_filter + 'trigger3'))
					{
						$('pure360_cart_' + this.scope_filter + 'trigger3').show();
						$('pure360_cart_' + this.scope_filter + 'trigger3').previous().previous().show();
					}
				} else
				{
					if ($('pure360_cart_' + this.scope_filter + 'trigger1'))
					{
						$('pure360_cart_' + this.scope_filter + 'trigger1').hide();
						$('pure360_cart_' + this.scope_filter + 'trigger1').previous().previous().hide();
					}
					if ($('pure360_cart_' + this.scope_filter + 'trigger2'))
					{
						$('pure360_cart_' + this.scope_filter + 'trigger2').hide();
						$('pure360_cart_' + this.scope_filter + 'trigger2').previous().previous().hide();
					}
					if ($('pure360_cart_' + this.scope_filter + 'trigger3'))
					{
						$('pure360_cart_' + this.scope_filter + 'trigger3').hide();
						$('pure360_cart_' + this.scope_filter + 'trigger3').previous().previous().hide();
					}
				}
			},
			saveTriggers: function() {
				var response, result, enabling;

				enabling = (this.enabledDefault.value === '0') && 
									(this.enabled.value === '1') ? '1': '0';

				$url = this.saveTriggerUrl
						+ "?scope=" + this.scope
						+ "&scopeId=" + this.scopeId,
						+ "&enabling=" + enabling;

				response = this.getHttp($url);

				result = JSON.parse(response);

				if (result === 'SUCCESS')
				{
					this.oldFormSubmit.call(this.configEditForm);
				}
				else
				{
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
