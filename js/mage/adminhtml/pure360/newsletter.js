Event.observe(window, 'load', function() {
	initPure360Newsletter();
});

Pure360Newsletter = Class.create();

Pure360Newsletter.prototype =
		{
			initialize: function(scope, scopeId) {
				this.scope = scope;
				this.scope_filter = (this.scope === 'default' ? 'default_' : this.scope + '_');
				this.scopeId = scopeId;
				this.enabled = document.getElementById('pure360_newsletter_' + this.scope_filter + 'settings_enabled');

				if (this.enabled)
				{
					// Setup observers
					var self = this;

					this.enabled.onchange = function(e) {
						return self.toggleEnabled();
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
			toggleEnabled: function() {

				if (this.enabled.value === '1')
				{
					if ($('pure360_newsletter_' + this.scope_filter + 'checkout'))
					{
						$('pure360_newsletter_' + this.scope_filter + 'checkout').show();
						$('pure360_newsletter_' + this.scope_filter + 'checkout').previous().previous().show();
					}
				} else
				{
					if ($('pure360_newsletter_' + this.scope_filter + 'checkout'))
					{
						$('pure360_newsletter_' + this.scope_filter + 'checkout').hide();
						$('pure360_newsletter_' + this.scope_filter + 'checkout').previous().previous().hide();
					}
				}
			}
		}
