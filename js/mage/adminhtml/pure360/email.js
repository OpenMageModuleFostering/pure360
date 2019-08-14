Event.observe(window, 'load', function() {
	initPure360Email();
});

Pure360Email = Class.create();

Pure360Email.prototype =
{
	initialize: function(scope, scopeId, saveMessagesUrl) {
		this.scope = scope;
		this.scope_filter = (this.scope === 'default' ? 'default_' : this.scope + '_');
		this.scopeId = scopeId;

		this.saveMessagesUrl	= saveMessagesUrl;		
		this.enabled			= document.getElementById('pure360_email_' + this.scope_filter + 'settings_enabled');
	
		if(this.enabled)
		{
			this.enabledDefault	= this.enabled.value;
			this.configEditForm	= document.getElementById('config_edit_form'); 
			this.oldFormSubmit	= this.configEditForm.submit;

			// Setup observers
			var self=this; 

			this.enabled.onchange = function(e) { return self.toggleEnabled(); };
			this.configEditForm.submit = function(e) { return self.submitForm(); };
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
		if(this.enabled.value !== this.enabledDefault)
		{
			this.oldFormSubmit.call(this.configEditForm);
			
		} else
		{
			if(this.configEditForm.validator !== null)
			{
				if(!this.configEditForm.validator.options.onFormValidate())
				{
					return false;
				}
			}
			
			this.saveMessages();
		}
	},
	toggleEnabled: function() {
		
	},
	saveMessages: function() {
		var response, result;

		var templateIds = new Array();
		$$("select[name=pure360_email_template_ids[]]").each(function(el)
		{
			templateIds.push($(el).value)
		});		

		var messageIds = new Array();
		$$("select[name=pure360_email_message_ids[]]").each(function(el)
		{
			messageIds.push($(el).value)
		});		
		
		$url = this.saveMessagesUrl
			+ "?scope=" + this.scope
			+ "&scopeId=" + this.scopeId
			+ "&templateIds=" + templateIds
			+ "&messageIds=" + messageIds;
	
		response = this.getHttp($url);

		location.reload(true);
	},	
	addMapping: function(elem) {
		var response, result;

		var _first	= $$('tr.addMappingRow')[0];
		var _last	= $$('tr.addButtonRow')[0];	
		var _new	= _first.cloneNode(true);
		
		_new.select('select').each(function(el)
		{
			var options = $(el).select('option'); 
			var len = options.length;
			for (var i = 0; i < len; i++) {
			  options[i].selected = false;
			}
		});
		
		var parentDiv = _last.parentNode;
		
		parentDiv.insertBefore(_new, _last);

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
