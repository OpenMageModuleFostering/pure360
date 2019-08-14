Event.observe(window, 'load', function() {
	initPure360();
});

Pure360 = Class.create();

Pure360.prototype = 
{
	initialize: function(scope, validateUrl) 
	{
		this.validateUrl		= validateUrl;
		this.scope				= scope;
		this.scope_filter		= (this.scope === 'default' ? 'default_' : this.scope + '_');		
		
		this.txtError			= this.translate('Credentials not validated.');
		this.txtNoSystemLogin	= this.translate('Please provide a System Login');
		this.txtSaveChanges		= this.translate('Credentials validated successfully. Please save the configuration before continuing.');

		this.enabled			= document.getElementById('pure360_'+this.scope_filter+'settings_enabled');
		
		if(this.enabled)
		{
			this.global				= document.getElementById('pure360_'+this.scope_filter+'settings_global');
			this.url				= document.getElementById('pure360_'+this.scope_filter+'settings_api_url');							

			this.head_marketing						= document.getElementById('pure360_'+this.scope_filter+'settings_marketing-head');
			this.username_marketing					= document.getElementById('pure360_'+this.scope_filter+'settings_marketing_username');
			this.password_marketing					= document.getElementById('pure360_'+this.scope_filter+'settings_marketing_password');
			this.validate_marketing					= document.getElementById('pure360_settings_marketing_validate');
			this.validated_marketing				= document.getElementById('pure360_'+this.scope_filter+'settings_marketing_validated');
			this.validatedDefault_marketing			= document.getElementById('pure360_'+this.scope_filter+'settings_marketing_validated').value;

			this.head_transactional					= document.getElementById('pure360_'+this.scope_filter+'settings_transactional-head');
			this.enabled_transactional				= document.getElementById('pure360_'+this.scope_filter+'settings_transactional_enabled');
			this.username_transactional				= document.getElementById('pure360_'+this.scope_filter+'settings_transactional_username');
			this.password_transactional				= document.getElementById('pure360_'+this.scope_filter+'settings_transactional_password');
			this.validate_transactional				= document.getElementById('pure360_settings_transactional_validate');
			this.validated_transactional			= document.getElementById('pure360_'+this.scope_filter+'settings_transactional_validated');
			this.validatedDefault_transactional		= document.getElementById('pure360_'+this.scope_filter+'settings_transactional_validated').value;
			
			// Hide hidden element labels
			$('row_pure360_'+this.scope_filter+'settings_transactional_validated').select('.scope-label').each(function(e) {
				e.hide(); 
			});

			// Setup observers
			var self=this;

			if(this.global) 
			{
				this.global.onchange = function(e) { return self.checkEnabled(); };
			}
			
			this.enabled.onchange					= function(e) { return self.checkEnabled(); };
			this.enabled_transactional.onchange		= function(e) { return self.checkEnabled(); };
			this.username_marketing.onchange		= function(e) { return self.checkValidated('marketing'); };
			this.password_marketing.onchange		= function(e) { return self.checkValidated('marketing'); };
			this.username_transactional.onchange	= function(e) { return self.checkValidated('transactional'); };
			this.password_transactional.onchange	= function(e) { return self.checkValidated('transactional'); };
			this.url.onchange						= function(e) { return self.checkValidated('marketing') && self.checkValidated('transactional'); };
			
			this.checkEnabled();
			this.checkValidated('marketing');
			this.checkValidated('transactional');
		}
	},
	translate: function(text) 
	{
		try {
			if(Translator){
				return Translator.translate(text);
			}
		}
		catch(e){}
		return text;
	},
	checkEnabled: function() 
	{
		if(this.global)
		{
			if (this.enabled.value === '1') 
			{
				$(this.global).up().show();
				
			} else 
			{
				$(this.global).up().hide();
			}
		}
	
		if(this.enabled_transactional.value === '0')
		{
			this.username_transactional.disabled = true;
			this.password_transactional.disabled = true;
			this.validated_transactional.disabled = true;
			
			$(this.validate_transactional).up().up().hide();
					
			if($('advice-required-entry-pure360_'+this.scope_filter+'settings_transactional_validated'))
			{
				$('advice-required-entry-pure360_'+this.scope_filter+'settings_transactional_validated').hide();
			}
		} else
		{
			this.username_transactional.disabled = false;
			this.password_transactional.disabled = false;
			this.validated_transactional.disabled = false;
			this.checkValidated('transactional');
		}
		
		if (this.enabled.value === '1' && (this.global === null || this.global.value === '1')) 
		{
			this.username_marketing.disabled = false;
			this.password_marketing.disabled = false;
			this.validated_marketing.disabled = false;
			
			if(this.enabled_transactional.value === '1')
			{
				this.username_transactional.disabled = false;
				this.password_transactional.disabled = false;
				this.validated_transactional.disabled = false;
			}
			
			$(this.url).up().up().show();
			$(this.head_marketing).up().show();
			$(this.head_marketing).up().next().next().show();
			
			$(this.head_transactional).up().show();
			$(this.head_transactional).up().next().next().show();

		} else 
		{
			this.username_marketing.disabled = true;
			this.password_marketing.disabled = true;
			this.validated_marketing.disabled = true;
			
			$(this.url).up().up().hide();
			$(this.head_marketing).up().hide();
			$(this.head_marketing).up().next().next().hide();
			$(this.head_transactional).up().hide();
			$(this.head_transactional).up().next().next().hide();
		}
	},
	checkValidated: function(type) 
	{
		if(type === 'marketing')
		{
			if( this.username_marketing.value !== this.username_marketing.defaultValue ||
				this.password_marketing.value !== this.password_marketing.defaultValue ||
				this.url.value !== this.url.defaultValue) 
			{
				this.validated_marketing.value = '';
				$(this.validate_marketing).up().up().show();
			} 
			else 
			{
				this.validated_marketing.value = this.validatedDefault_marketing;
				$(this.validate_marketing).up().up().hide();
			
				if($('advice-required-entry-pure360_'+this.scope_filter+'settings_marketing_validated'))
				{
					$('advice-required-entry-pure360_'+this.scope_filter+'settings_marketing_validated').hide();
				}
			}
		}
		else if(type === 'transactional')
		{
			if( this.username_transactional.value !== this.username_transactional.defaultValue ||
				this.password_transactional.value !== this.password_transactional.defaultValue ||
				this.url.value !== this.url.defaultValue || 
				(!this.username_transactional.value || !this.password_transactional.value) &&
				this.enabled_transactional.value === '1'
			)
			{
				this.validated_transactional.value = '';
				$(this.validate_transactional).up().up().show();
			} 
			else 
			{
				this.validated_transactional.value = this.validatedDefault_transactional;
				$(this.validate_transactional).up().up().hide();
			
				if($('advice-required-entry-pure360_'+this.scope_filter+'settings_transactional_validated'))
				{
					$('advice-required-entry-pure360_'+this.scope_filter+'settings_transactional_validated').hide();
				}
			}
		}
	},
	validate: function(type) 
	{
		var response, result;
		
		if(type==='marketing')
		{
			$(this.validated_marketing).setValue('');
			var _url = this.validateUrl 
				+ "?url=" + this.url.value 
				+ "&username=" + this.username_marketing.value
				+ "&password=" + this.password_marketing.value;
			
			response = this.getHttp(_url);
		}
		else if(type==='transactional')
		{
			$(this.validated_transactional).setValue('');
			var _url = this.validateUrl 
				+ "?url=" + this.url.value 
				+ "&username=" + this.username_transactional.value
				+ "&password=" + this.password_transactional.value;
			
			response = this.getHttp(_url);
		}
		
		// Parse json data into object
		var responseObject = JSON.parse(response);
 
		// Check for errors
		if(responseObject.error)
		{				
			this.alertError(responseObject.error);
				
		} else if (responseObject.bus_entity_account_login.systemLoginInd !== 'Y') {
				
			this.alertError(this.txtNoSystemLogin);
				
		} else 
		{
			this.url.defaultValue = this.url.value;
						
			if(type==='marketing')
			{
				this.validated_marketing.value = 1;
				this.validated_marketing.defaultValue = 1;
				this.username_marketing.defaultValue = this.username_marketing.value;
				this.password_marketing.defaultValue = this.password_marketing.value;
				
				$(this.validate_marketing).up().up().hide();
			}
			else if(type==='transactional')
			{
				this.validated_transactional.value = 1;
				this.validated_transactional.defaultValue = 1;
				this.username_transactional.defaultValue = this.username_transactional.value;
				this.password_transactional.defaultValue = this.password_transactional.value;
				
				$(this.validate_transactional).up().up().hide();
			}
			
			if($('advice-required-entry-pure360_'+this.scope_filter+'settings_api_url'))
				$('advice-required-entry-pure360_'+this.scope_filter+'settings_api_url').hide();
			
			if($('advice-required-entry-pure360_'+this.scope_filter+'settings_'+type+'_username'))
				$('advice-required-entry-pure360_'+this.scope_filter+'settings_'+type+'_username').hide();
								
			if($('advice-required-entry-pure360_'+this.scope_filter+'settings_'+type+'_password'))
				$('advice-required-entry-pure360_'+this.scope_filter+'settings_'+type+'_password').hide();

			if($('advice-required-entry-pure360_'+this.scope_filter+'settings_'+type+'_validated'))
				$('advice-required-entry-pure360_'+this.scope_filter+'settings_'+type+'_validated').hide();
											
			this.alertSaveChanges();
		}

	},
	alertError: function (error) 
	{
		alert(this.txtError + '\n('+error+')');
	},
	alertSaveChanges: function () 
	{
		alert(this.txtSaveChanges);
	},
	getHttp: function (url) 
	{
		var response;
		new Ajax.Request(
			url,
			{
				method:       "get",
				onComplete:   function(transport) {
					response = transport.responseText;
				},
				asynchronous: false
			});
		return response;
	},
	getInteger: function (number) 
	{
		number = parseInt(number);
		if (isNaN(number)) return 0;
		return number;
	}
}
