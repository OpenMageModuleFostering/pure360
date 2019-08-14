<?php

/**
 * @package   Pure360\Email
 * @copyright 2013 Pure360.com
 */
class Pure360_Email_Model_Email_Message extends Mage_Core_Model_Email_Template
{

	/**
	 * Send transactional email to recipient
	 *
	 * @param int          $templateId
	 * @param string|array $sender     Sender information, can be declared as part of config path
	 * @param string       $email      Recipient email
	 * @param string       $name       Recipient name
	 * @param array        $vars       Variables which can be used in template
	 * @param int|null     $storeId
	 *
	 * @return Mage_Core_Model_Email_Template
	 */
	public function sendTransactional($templateId, $sender, $email, $name, $vars = array(), $storeId = null)
	{
		if(($storeId === null) && $this->getDesignConfig()->getStore())
		{
			$storeId = $this->getDesignConfig()->getStore();
		}

		if($storeId)
		{
			if(!Mage::helper('pure360_email')->isEnabledForStore($storeId))
			{
				return parent::sendTransactional($templateId, $sender, $email, $name, $vars, $storeId);
			}

		} else if (!Mage::helper('pure360_email')->isEnabled(true))
		{
			return parent::sendTransactional($templateId, $sender, $email, $name, $vars, $storeId);
		}
		
		// Get scope for storeId
		$scope		= Mage::helper('pure360_common')->getActiveModuleScopeForStore('pure360_email', $storeId);
		$scopeId	= null;
		
		switch($scope)
		{
			case 'stores' :
			{
				$scopeId = $storeId;
				break;
			}
			case 'websites' :
			{
				$store = Mage::getModel("core/store")->load($storeId);
				$scopeId = $store->getWebsiteId();
				break;
			}
			case 'default' :
			{
				$scopeId = 0;
				break;
			}
		}
		
		// Load template
		if(is_numeric($templateId))
		{
			$this->load($templateId);
			
		} else
		{
			$localeCode = Mage::getStoreConfig('general/locale/code', $storeId);
			$this->loadDefault($templateId, $localeCode);
		}
		
		Mage::helper('pure360_email')->writeDebug(__METHOD__ . ' - start');

		$this->setSentSuccess(false);
		
		if(!$this->getId())
		{
			throw Mage::exception('Mage_Core', Mage::helper('core')->__('Invalid transactional email code: ' . $templateId));
		}

		if(!is_array($sender))
		{
			$this->setSenderName(Mage::getStoreConfig('trans_email/ident_' . $sender . '/name', $storeId));
			$this->setSenderEmail(Mage::getStoreConfig('trans_email/ident_' . $sender . '/email', $storeId));
		
		} else
		{
			$this->setSenderName($sender['name']);
			$this->setSenderEmail($sender['email']);
		}

		if(!isset($vars['store']))
		{
			$vars['store'] = Mage::app()->getStore($storeId);
		}
		
		$vars['storeId'] = $storeId;
		
		$this->setSentSuccess($this->pure360Send($templateId, $email, $name, $vars));
		
		Mage::helper('pure360_email')->writeDebug(__METHOD__ . ' - end');
		
		return $this;
	}

	/**
	 * Send mail to recipient
	 *
	 * @param int      $messageId     Pure360 MessageId
	 * @param array|string      $email     E-mail(s)
	 * @param array|string|null $name      receiver name(s)
	 * @param array             $variables template variables
	 *
	 * @return boolean
	 */
	public function pure360Send($templateId, $email, $name, $variables)
	{
		Mage::helper('pure360_email')->writeDebug(__METHOD__ . ' - start');
		
        $emails = array_values((array)$email);
        $names = is_array($name) ? $name : (array)$name;
        $names = array_values($names);
        foreach ($emails as $key => $email) {
            if (!isset($names[$key])) {
                $names[$key] = substr($email, 0, strpos($email, '@'));
            }
        }

		$variables['email'] = reset($emails);
		$variables['name'] = reset($names);
		
		$this->setUseAbsoluteLinks(true);
		$text = $this->getProcessedTemplate($variables, true);
		$plainText = $text;

		if(!$this->isPlain())
		{

			$searchPatterns = array(
				'/<style.*>.*<\/style>/Usi',
				'/<p.*>/Ui',
				'/<\/p>/i',
				'/<br.*\/?>/i',
				'/<[^>]+>/Usi',
			);
			$replaceValues = array(
				'',
				"\r\n\r\n",
				"\r\n",
				"\r\n",
				'',
			);
			$plainText = preg_replace($searchPatterns, $replaceValues, $plainText);
		}

		try
		{
			// Create API Client for current context
			$storeId			= $variables['storeId'];
			$url				= Mage::helper('pure360_common')->getModuleGroupKeyValue('pure360', 'settings', 'api_url', $storeId);
			$username			= Mage::helper('pure360_common')->getModuleGroupKeyValue('pure360', 'settings_transactional', 'username', $storeId);
			$password			= Mage::helper('pure360_common')->getModuleGroupKeyValue('pure360', 'settings_transactional', 'password', $storeId);
			$client				= Mage::helper('pure360_common/api')->getClient($url, $username, $password);
			
			// Send using Pure360 One2One
			// Todo: allow modification of the plain text message. Currently, only stripping the tags out of the HTML message.
			$messageSubject		= $this->getProcessedTemplateSubject($variables);
			$messageName		= $templateId;
			$messageBodyHtml	= $this->isPlain() ? null : $text; 
			$messageBodyPlain	= $this->isPlain() ? $text : $plainText;
			$toAddress			= $variables['email'];
			$fromAddress		= $this->getSenderEmail();
			$fromDesc			= $this->getSenderName();

			Mage::helper('pure360_email/api')->sendOne2One($client, $messageSubject, $messageName, $messageBodyHtml, $messageBodyPlain, $toAddress, $fromAddress, $fromDesc);
			$this->_mail = null;
			
			$client->logout();
			
		} catch(Pure360_Exception_ValidationException $e)
		{
			$this->_mail = null;
			Mage::helper('pure360_email')->writeError(print_r($e->getErrors(), true));
			return false;
			
		} catch(Exception $e)
		{
			$this->_mail = null;
			Mage::helper('pure360_email')->writeError($e);
			return false;
		}

		Mage::helper('pure360_email')->writeDebug(__METHOD__ . ' - end');
		
		return true;
	}
}
