Event.observe(window, 'load', function() {
	initPure360Cron();
});

Pure360Cron = Class.create();

Pure360Cron.prototype =
		{
			initialize: function(scope, scopeId, updateCronUrl, listJobsUrl, forceJobUrl, pauseJobUrl, resumeJobUrl, downloadLogUrl, cleanLogUrl) {
				this.scope = scope;
				this.scope_filter = (this.scope === 'default' ? 'default_' : this.scope + '_');
				this.scopeId = scopeId;

				this.updateCronUrl = updateCronUrl;
				this.listJobsUrl = listJobsUrl;
				this.forceJobUrl = forceJobUrl;
				this.pauseJobUrl = pauseJobUrl;
				this.resumeJobUrl = resumeJobUrl;
				this.downloadLogUrl = downloadLogUrl;
				this.cleanLogUrl = cleanLogUrl;

				var self = this;
				this.configEditForm = document.getElementById('config_edit_form');
				this.oldFormSubmit = this.configEditForm.submit;
				this.configEditForm.submit = function(e) {
					return self.submitForm();
				};
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
				var response;
				var self = this;
				var errors = '';

				$$('[name=crontab]').each(function(e) {
					if ($(e).up().up().nodeName === 'TR')
					{
						var jobId = $(e).up().up().down().innerHTML.stripTags();
						var url = self.updateCronUrl
								+ "?id=" + jobId + '&value=' + encodeURIComponent($(e).getValue());
						response = self.getHttp(url);

						if (response.indexOf('Invalid') != -1)
						{
							errors += response;
						}
					}
				});

				if (errors != '')
				{
					alert(errors);
				}
				else
				{
					this.oldFormSubmit.call(this.configEditForm);
				}
			},
			refreshJobs: function() {

			},
			forceJob: function(jobId) {
				var answer = confirm("Please click on OK to force schedule this job to run on the next available slot, or CANCEL if this is not want you want to do.")
				if (answer)
				{
					var response;
					var url = this.forceJobUrl
							+ "?id=" + jobId;
					response = this.getHttp(url);
					this.oldFormSubmit.call(this.configEditForm);
				}
			},
			pauseJob: function(jobId) {
				var answer = confirm("Please click on OK to pause this schedule item. By doing so this job will not run again until you choose to resume. Or click CANCEL if this is not want you want to do.")
				if (answer)
				{
					var response;
					var url = this.pauseJobUrl
							+ "?id=" + jobId;
					response = this.getHttp(url);
					alert(response);
					location.reload(true);
				}
			},
			resumeJob: function(jobId) {
				var answer = confirm("Please click on OK to resume this schedule item. By doing so this job will run on next crontab setting. Or click CANCEL if this is not want you want to do.")
				if (answer)
				{
					var response;
					var url = this.resumeJobUrl
							+ "?id=" + jobId;
					response = this.getHttp(url);
					alert(response);
					location.reload(true);
				}
			},
			updateCron: function(button, jobId) {
				var response;
				var url = this.updateCronUrl
						+ "?id=" + jobId + '&value=' + encodeURIComponent($(button).previous('input').getValue());
				response = this.getHttp(url);
				alert(response);
			},
			download: function(fileName) {
				var response;
				var url = this.downloadLogUrl + "?fileName=" + encodeURIComponent(fileName);
				window.location = url;
			},
			clean: function(fileName) {
				var response;
				var url = this.cleanLogUrl + "?fileName=" + encodeURIComponent(fileName);				
				response = this.getHttp(url);
				location.reload(true);
			},
			alertSaveChanges: function() {

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
