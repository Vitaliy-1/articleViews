<?php

import('lib.pkp.classes.plugins.GenericPlugin');

class ArticleViewsPlugin extends GenericPlugin {

	function register($category, $path, $mainContextId = null) {
		$success = parent::register($category, $path, $mainContextId);
		if (!Config::getVar('general', 'installed') || defined('RUNNING_UPGRADE')) return $success;

		if ($success && $this->getEnabled($mainContextId)) {
			HookRegistry::register('Templates::Issue::Issue::Article', array($this, 'callbackArticleSummary'));
			HookRegistry::register('TemplateManager::display', array($this, 'templateMgrDisplay'));
		}
		return $success;
	}

	/**
	 * Get the display name of this plugin
	 * @return string
	 */
	function getDisplayName() {
		return __('plugins.generic.articleViews.name');
	}

	/**
	 * Get the description of this plugin
	 * @return string
	 */
	function getDescription() {
		return __('plugins.generic.articleViews.description');
	}

	/**
	 * @param $hookname string
	 * @param $args array
	 * @brief Add data to the issue TOC related pages
	 */
	function callbackArticleSummary($hookname, $args) {
		$smarty =& $args[1];
		$output =& $args[2];
		$article = $smarty->getTemplateVars('article'); /* @var $article PublishedArticle */
		$views = $article->getViews();

		$galleyDao = DAORegistry::getDAO('ArticleGalleyDAO'); /* @var $galleyDao ArticleGalleyDAO */
		$galleysFactory = $galleyDao->getBySubmissionId($article->getId(), $this->getCurrentContextId()); /* @var $galleysFactory DAOResultFactory */
		$galleyViews = 0;
		foreach ($galleysFactory->toArray() as $galley) {
			/* @var $galley ArticleGalley */
			$galleyViews += $galley->getViews();
		}

		$smarty->assign('views', $views);
		$smarty->assign([
			'views' => $views,
			'galleyViews' => $galleyViews,
		]);
		$output .= $smarty->fetch($this->getTemplateResource('articleViews.tpl'));
	}

	/**
	 * @param $hookname string
	 * @param $args array
	 * @brief Add Styling related to article views count
	 */
	function templateMgrDisplay($hookname, $args) {
		$smarty =& $args[0];
		$template = $args[1];
		if ($template != 'frontend/pages/issue.tpl' && $template != 'frontend/pages/indexJournal.tpl') return false;
		$baseUrl = $this->getRequest()->getBaseUrl() . '/' . $this->getPluginPath();
		$smarty->addStyleSheet('articleViews', $baseUrl . '/styles/general.css');
	}
}

?>
