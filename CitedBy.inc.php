<?php

/**
 * @file plugins/generic/citedBy/CitedBy.inc.php
 *
 * Roberto Camargo @btocamargo
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CitedBy
 *
 * @brief Plugin Cited by displays the number of times it was cited and links to the content of the citation.
 */
import('lib.pkp.classes.plugins.GenericPlugin');
import('plugins.importexport.crossref.CrossRefExportPlugin');


class CitedBy extends GenericPlugin {

	var $url = 'https://doi.crossref.org/servlet/getForwardLinks?';

	function register($category, $path, $mainContextId = null) {

		$success = parent::register($category, $path, $mainContextId);
		if (!Config::getVar('general', 'installed') || defined('RUNNING_UPGRADE')) return $success;

		if ($success && $this->getEnabled($mainContextId)) {
			HookRegistry::register('Templates::Article::Main', array($this, 'addCitation'));
		}

		return $success;
	}

	public function getDisplayName() {
		return 'Cited-by';
	}

	public function get_url(){
		return  $this->url;
	}
	public function getDescription() {
		return 'This plugin shows how work has been received by the wider community; displaying the number of times it has been cited. Cited-by allows Crossref members to find out who is citing their content.';
	}

	public function getDOI(){
		$templateMgr =& TemplateManager::getManager();
		$article = $templateMgr->get_template_vars('article');

		return $article->getStoredPubId('doi');
	}


	public function addCitation($hookName, $params) {
		$smarty =& $params[1];
		$output =& $params[2];


		$crosSettings = new CrossRefExportPlugin();

		$request = $this->getRequest();
		$context = $request->getContext();

		$usr = $crosSettings->getSetting($context->getId(), 'username');
		$pwd = $crosSettings->getSetting($context->getId(), 'password');
		$doi = $this->getDOI();

		$data = array('usr'=>$usr,'pwd'=>$pwd,'doi'=>$doi);
		$query = http_build_query($data);
		

		$citation = simplexml_load_file($this->url.$query);

		
		$total =  count($citation->query_result->body->forward_link);


		if($total > 0){
			$smarty->assign('citedby', $citation);
			$smarty->assign('total', $total);
			$output .= $smarty->fetch($this->getTemplateResource('citedBy.tpl'));
		}

		return false;
	}
	
}

