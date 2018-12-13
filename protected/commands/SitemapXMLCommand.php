<?php
/*Created by Кирилл (21.05.2018 22:12)*/
ini_set('max_execution_time', 3600);
/** /usr/bin/php /var/www/www-root/data/ruslania2.ptysh.ru/command.php sitemapxml
 * Class SitemapXMLCommand
 */
define('cronAction', 1);
class SitemapXMLCommand extends CConsoleCommand {
	protected $_counts = 49500; //кол-во записей в файле
	protected $_isLastmod = true, //дата последнего изменения, если true
		$_isPriority = true,//приоритет 0.0 - 1.0, если true
		$_isChangefreq = false;//always, hourly, daily, weekly, monthly, yearly, never, если true

	protected $_dir;//папка для файлов

	public function actionIndex() {
		$this->_dir = Yii::getPathOfAlias('webroot') . '/pictures/sitemap/';
		if (!file_exists($this->_dir)) mkdir($this->_dir, 0755, true);
		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><sitemapindex></sitemapindex>');
		$xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
		$xml->addAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$xml->addAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd');

		$smapHtml = new Sitemap();
		$lang = 'ru';
		foreach (Yii::app()->params['ValidLanguages'] as $lang) {
			if ($lang !== 'rut') {
				Yii::app()->setLanguage($lang);
				echo Yii::app()->getLanguage() . "\r\n";
				echo "StaticPages ".date('d.m.Y H:i:s')."\r\n";
//				Yii::app()->getUrlManager()->setLanguage(Yii::app()->getLanguage());
				list($file, $lastmod) = $this->_staticXml($smapHtml);
				if ($file&&file_exists($file)) {
					$urlXml = $xml->addChild('sitemap');
					$urlXml->addChild('loc', Yii::app()->urlManager->getBaseUrl() . '/pictures/sitemap/' .basename($file));
					$urlXml->addChild('lastmod', date('c', $lastmod));
				}

				echo "ItemPages ".date('d.m.Y H:i:s')."\r\n";
				$files = $this->_itemsXml();
				foreach ($files as $file=>$lastmod) {
					if ($file&&file_exists($file)) {
						$urlXml = $xml->addChild('sitemap');
						$urlXml->addChild('loc', Yii::app()->urlManager->getBaseUrl() . '/pictures/sitemap/' .basename($file));
						$urlXml->addChild('lastmod', date('c', $lastmod));
					}
				}

				echo "CategoryPages ".date('d.m.Y H:i:s')."\r\n";
				list($file, $lastmod) = $this->_categoryXml();
				if ($file&&file_exists($file)) {
					$urlXml = $xml->addChild('sitemap');
					$urlXml->addChild('loc', Yii::app()->urlManager->getBaseUrl() . '/pictures/sitemap/' .basename($file));
					$urlXml->addChild('lastmod', date('c', $lastmod));
				}

				echo "tagsPages ".date('d.m.Y H:i:s')."\r\n";
				$files = $this->_tagsXml($smapHtml);
				foreach ($files as $file=>$lastmod) {
					if ($file&&file_exists($file)) {
						$urlXml = $xml->addChild('sitemap');
						$urlXml->addChild('loc', Yii::app()->urlManager->getBaseUrl() . '/pictures/sitemap/' .basename($file));
						$urlXml->addChild('lastmod', date('c', $lastmod));
					}
				}

				echo "offersPages ".date('d.m.Y H:i:s')."\r\n";
				list($file, $lastmod) = $this->_offersXml();
				if ($file&&file_exists($file)) {
					$urlXml = $xml->addChild('sitemap');
					$urlXml->addChild('loc', Yii::app()->urlManager->getBaseUrl() . '/pictures/sitemap/' .basename($file));
					$urlXml->addChild('lastmod', date('c', $lastmod));
				}

				echo "bookshelfPages ".date('d.m.Y H:i:s')."\r\n";
				list($file, $lastmod) = $this->_bookshelfXml();
				if ($file&&file_exists($file)) {
					$urlXml = $xml->addChild('sitemap');
					$urlXml->addChild('loc', Yii::app()->urlManager->getBaseUrl() . '/pictures/sitemap/' .basename($file));
					$urlXml->addChild('lastmod', date('c', $lastmod));
				}
			}
		}


		$xml->asXML($this->_dir . 'sitemap.xml');
	}

	private function _bookshelfXml() {
		$lastmod = 0;
		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><urlset></urlset>');
		$xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

		$items = $this->_query($this->_sqlBookshelf());
		if($items->count() > 0) {
			foreach ($items as $item) {
				$url = Yii::app()->createUrl('/bookshelf/view', array('id' => $item['id'], '__langForUrl'=>Yii::app()->getLanguage()));
				$urlXml = $xml->addChild('url');
				$urlXml->addChild('loc', $url);
				$lastmod = max($lastmod, (int)$item['dateAdd']);
				if ($this->_isLastmod) $urlXml->addChild('lastmod', date('c', (int)$item['dateAdd']));
				if ($this->_isChangefreq) $urlXml->addChild('changefreq', 'never');
				if ($this->_isPriority) $urlXml->addChild('priority', '0.6');
			}
			unset($items);
		}

		$url = Yii::app()->createUrl('bookshelf/list', array('__langForUrl'=>Yii::app()->getLanguage()));
		$urlXml = $xml->addChild('url');
		$urlXml->addChild('loc', $url);
		if (!empty($lastmod)&&$this->_isLastmod) $urlXml->addChild('lastmod', date('c', $lastmod));
		if ($this->_isChangefreq) $urlXml->addChild('changefreq', 'never');
		if ($this->_isPriority) $urlXml->addChild('priority', '0.6');

		return [$this->_saveFile($xml, 'sitemap-bookshelf.xml'), $lastmod];

	}

	private function _offersXml() {
		//не знаю как получить список констант из класса Offer и связь с тегом
		$const = array(
			Offer::INDEX_PAGE => 'index',
			Offer::FIRMS => 'firms',
			Offer::LIBRARY => 'lib',
			Offer::UNI => 'uni',
			Offer::FREE_SHIPPING => 'fs',
			Offer::ALLE_2_EURO => 'alle2',
		);
		//Yii::app()->createUrl('offers/view', array('oid' => $item['id'], 'title' => ProductHelper::ToAscii($title)));

		$lastmod = 0;
		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><urlset></urlset>');
		$xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

		$items = $this->_query($this->_sqlOffers());
		if($items->count() > 0) {
			foreach ($items as $item) {
				if (isset($const[$item['id']])) {
					if ($const[$item['id']] == 'index') continue;
					$url = Yii::app()->createUrl('offers/special', array('mode' => $const[$item['id']], '__langForUrl'=>Yii::app()->getLanguage()));
				}
				else $url = Yii::app()->createUrl('offers/view', array('oid' => $item['id'], 'title' => ProductHelper::ToAscii($item['title']), '__langForUrl'=>Yii::app()->getLanguage()));
				$urlXml = $xml->addChild('url');
				$urlXml->addChild('loc', $url);
				$lastmod = max($lastmod, (int)$item['dateAdd']);
				if ($this->_isLastmod) $urlXml->addChild('lastmod', date('c', (int)$item['dateAdd']));
				if ($this->_isChangefreq) $urlXml->addChild('changefreq', 'never');
				if ($this->_isPriority) $urlXml->addChild('priority', '0.6');
			}
			unset($items);
		}

		$url = Yii::app()->createUrl('offers/list', array('__langForUrl'=>Yii::app()->getLanguage()));
		$urlXml = $xml->addChild('url');
		$urlXml->addChild('loc', $url);
		if (!empty($lastmod)&&$this->_isLastmod) $urlXml->addChild('lastmod', date('c', $lastmod));
		if ($this->_isChangefreq) $urlXml->addChild('changefreq', 'never');
		if ($this->_isPriority) $urlXml->addChild('priority', '0.6');

		return [$this->_saveFile($xml, 'sitemap-offers.xml'), $lastmod];

	}

	private function _staticXml(Sitemap $smapHtml) {
		$save = false;
		$staticDir = Yii::getPathOfAlias('webroot') . '/pictures/templates-static/';
		$lastmod = 0;

		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><urlset></urlset>');
		$xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

//		$url = Yii::app()->urlManager->getBaseUrl();
		$url = Yii::app()->createUrl('site/index', array('__langForUrl'=>Yii::app()->getLanguage()));
		$urlXml = $xml->addChild('url');
		$urlXml->addChild('loc', $url);
		if ($this->_isChangefreq) $urlXml->addChild('changefreq', 'monthly');
		if ($this->_isPriority) $urlXml->addChild('priority', '0.9');

		/*foreach ($pages as $page=>$name) {
			$save = true;
			$url = Yii::app()->createUrl('site/static', array('page' => $page));
			$urlXml = $xml->addChild('url');
			$urlXml->addChild('loc', $url);
			if ($this->_isChangefreq) $urlXml->addChild('changefreq', 'monthly');
			if ($this->_isPriority) $urlXml->addChild('priority', '0.9');
		}*/

		foreach (StaticUrlRule::getTitles() as $page=>$name) {
			$save = true;
			$url = Yii::app()->createUrl('site/static', array('page' => $page, '__langForUrl'=>Yii::app()->getLanguage()));
			$urlXml = $xml->addChild('url');
			$urlXml->addChild('loc', $url);
			if ($this->_isChangefreq) $urlXml->addChild('changefreq', 'monthly');
			if ($this->_isPriority) $urlXml->addChild('priority', '0.9');
		}

		list($tags, $tagsAll, $tagsHand) = $smapHtml->getTags();
		foreach ($tagsHand as $tag=>$param) {
			$save = true;
			$url = Yii::app()->createUrl($param[2], empty($param[3])?array('__langForUrl'=>Yii::app()->getLanguage()):array_merge(array('__langForUrl'=>Yii::app()->getLanguage(), $param[3])));
			$urlXml = $xml->addChild('url');
			$urlXml->addChild('loc', $url);
			if ($this->_isLastmod) $urlXml->addChild('lastmod', date('c', time()));
			if ($this->_isChangefreq) $urlXml->addChild('changefreq', 'monthly');
			if ($this->_isPriority) $urlXml->addChild('priority', '0.9');
		}

		$sitemapFile = '';
		if ($save) $sitemapFile = $this->_saveFile($xml, 'sitemap_static.xml');
		return [$sitemapFile, $lastmod];
	}

	private function _itemsXml() {
		$files = array();//файл=>lastmod
		foreach (Entity::GetEntitiesList() as $entity=>$params) {
			$step = 0;
			while (($items = $this->_query($this->_sqlItems($params['site_table'], $step++)))&&($items->count() > 0)) {
				$lastmod = 0;
				$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><urlset></urlset>');
				$xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
				foreach ($items as $item) {
					$title = ProductHelper::ToAscii($item['title']);
					$urlParams = array(
						'entity' => $entity,
						'id' => $item['id'],
						'title' => $title,
						'__useTitleParams'=>true,
						'__langForUrl'=>Yii::app()->getLanguage(),
					);
					if (!empty($lang)&&($lang !== Yii::app()->getLanguage())&&!defined('OLD_PAGES')) $urlParams['__langForUrl'] = $lang;
					$url = Yii::app()->createUrl('product/view', $urlParams);
					$urlXml = $xml->addChild('url');
					$urlXml->addChild('loc', $url);
					$lastmod = max($lastmod, (int)$item['dateAdd']);
					if ($this->_isLastmod) $urlXml->addChild('lastmod', date('c', (int)$item['dateAdd']));
					if ($this->_isChangefreq) $urlXml->addChild('changefreq', 'never');
					if ($this->_isPriority) $urlXml->addChild('priority', '0.8');
				}
				unset($items);
				$files[$this->_saveFile($xml, 'sitemap-' . $entity . '-' . $step . '.xml')] = $lastmod;
			}
		}
		return $files;
	}

	private function _categoryXml() {
		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><urlset></urlset>');
		$xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
		$lastmod = 0;


		foreach (Entity::GetEntitiesList() as $entity=>$params) {
			$rootLastmod = 0;
			$items = $this->_query($this->_sqlCategorys($params['site_table'], $params['site_category_table']));
			if ($items->count() > 0) {
				foreach ($items as $item) {
					$url = Yii::app()->createUrl('entity/list', array(
						'entity' => Entity::GetUrlKey($entity),
						'cid' => $item['id'],
						'title'=>ProductHelper::ToAscii($item['title']),
						'__langForUrl'=>Yii::app()->getLanguage(),
						'__useTitleParams'=>true,
					));
					$urlXml = $xml->addChild('url');
					$urlXml->addChild('loc', $url);
					$rootLastmod = max($rootLastmod, (int)$item['dateAdd']);
					if ($this->_isLastmod) $urlXml->addChild('lastmod', date('c', (int)$item['dateAdd']));
					if ($this->_isChangefreq) $urlXml->addChild('changefreq', 'never');
					if ($this->_isPriority) $urlXml->addChild('priority', '0.7');
				}
				unset($items);
			}
			$url = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity), '__langForUrl'=>Yii::app()->getLanguage()));
			$urlXml = $xml->addChild('url');
			$urlXml->addChild('loc', $url);
			if ($this->_isLastmod) $urlXml->addChild('lastmod', date('c', $rootLastmod));
			if ($this->_isChangefreq) $urlXml->addChild('changefreq', 'never');
			if ($this->_isPriority) $urlXml->addChild('priority', '0.7');
			$lastmod = max($lastmod, $rootLastmod);
		}
		return [$this->_saveFile($xml, 'sitemap-categories.xml'), $lastmod];
	}

	private function _tagsXml(Sitemap $smapHtml) {
		list($tags, $tagsAll, $tagsHand) = $smapHtml->getTags();
		$files = array();//файл=>lastmod


		$xmlRoot = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><urlset></urlset>');
		$xmlRoot->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
		$lastmodRoot = 0;
		foreach (Entity::GetEntitiesList() as $entity=>$params) {
			foreach ($tags as $tag=>$param) {
				$funcName = '_' . $tag . 'Xml';
				if ($smapHtml->checkTagByEntity($tag, $entity)&&method_exists($this, $funcName)) {
					$urlRoot = Yii::app()->createUrl('entity/' . $param[2], array('entity' => Entity::GetUrlKey($entity), '__langForUrl'=>Yii::app()->getLanguage()));
					$urlXmlRoot = $xmlRoot->addChild('url');
					$urlXmlRoot->addChild('loc', $urlRoot);
					foreach ($this->$funcName($entity, $params) as $file=>$lastmod) {
						$lastmodRoot = max($lastmodRoot, $lastmod);
						$files[$file] = $lastmod;
					}
					if ($this->_isLastmod) $urlXmlRoot->addChild('lastmod', date('c', $lastmodRoot));
					if ($this->_isChangefreq) $urlXmlRoot->addChild('changefreq', 'never');
					if ($this->_isPriority) $urlXmlRoot->addChild('priority', '0.6');
				}
			}
			foreach ($tagsAll as $tag=>$param) {
				$funcName = '_' . $tag . 'Xml';
				if (!in_array($params['site_table'], $param[4])&&method_exists($this, $funcName)) {
					//TODO:: gift доделать
					$urlRoot = Yii::app()->createUrl('entity/' . $param[2], array('entity' => Entity::GetUrlKey($entity), '__langForUrl'=>Yii::app()->getLanguage()));
					$urlXmlRoot = $xmlRoot->addChild('url');
					$urlXmlRoot->addChild('loc', $urlRoot);
					foreach ($this->$funcName($entity, $params) as $file=>$lastmod) {
						$lastmodRoot = max($lastmodRoot, $lastmod);
						$files[$file] = $lastmod;
					}
					if ($this->_isLastmod) $urlXmlRoot->addChild('lastmod', date('c', $lastmodRoot));
					if ($this->_isChangefreq) $urlXmlRoot->addChild('changefreq', 'never');
					if ($this->_isPriority) $urlXmlRoot->addChild('priority', '0.6');
				}
			}
		}
		$files[$this->_saveFile($xmlRoot, 'sitemap-tags.xml')] = $lastmodRoot;
		return $files;
	}

	private function _publisherXml($entity, $params) {
		$fields = array();

		$step = 0;
		while (($items = $this->_query($this->_sqlPublisher($params['site_table'], $step++)))&&($items->count() > 0)) {
			$lastmod = 0;
			$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><urlset></urlset>');
			$xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
			foreach ($items as $item) {
				$item['entity'] = $entity;
				$url = Yii::app()->createUrl('entity/bypublisher', array(
					'entity' => Entity::GetUrlKey($entity),
					'pid' => $item['id'],
					'__useTitleParams'=>true,
					'__langForUrl'=>Yii::app()->getLanguage(),
					'title' => ProductHelper::ToAscii($item['title']))
				);
				$urlXml = $xml->addChild('url');
				$urlXml->addChild('loc', $url);
				$lastmod = max($lastmod, (int)$item['dateAdd']);
				if ($this->_isLastmod) $urlXml->addChild('lastmod', date('c', (int)$item['dateAdd']));
				if ($this->_isChangefreq) $urlXml->addChild('changefreq', 'never');
				if ($this->_isPriority) $urlXml->addChild('priority', '0.6');
			}
			unset($items);
			$fields[$this->_saveFile($xml, 'sitemap-' . $params['entity'] . '-publisher-' . $step . '.xml')] = $lastmod;
		}
		return $fields;
	}

	private function _seriesXml($entity, $params) {
		$fields = array();

		$step = 0;
		while (($items = $this->_query($this->_sqlSeries($params['site_table'], $params['site_series_table'], $step++)))&&($items->count() > 0)) {
			$lastmod = 0;
			$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><urlset></urlset>');
			$xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
			foreach ($items as $item) {
				$item['entity'] = $entity;
				$url = Yii::app()->createUrl('entity/byseries', array(
					'entity' => Entity::GetUrlKey($entity),
					'sid' => $item['id'],
					'__useTitleParams'=>true,
					'__langForUrl'=>Yii::app()->getLanguage(),
					'title' => ProductHelper::ToAscii($item['title']))
				);
				$urlXml = $xml->addChild('url');
				$urlXml->addChild('loc', $url);
				$lastmod = max($lastmod, (int)$item['dateAdd']);
				if ($this->_isLastmod) $urlXml->addChild('lastmod', date('c', (int)$item['dateAdd']));
				if ($this->_isChangefreq) $urlXml->addChild('changefreq', 'never');
				if ($this->_isPriority) $urlXml->addChild('priority', '0.6');
			}
			unset($items);
			$fields[$this->_saveFile($xml, 'sitemap-' . $params['entity'] . '-series-' . $step . '.xml')] = $lastmod;
		}
		return $fields;
	}

	private function _authorsXml($entity, $params) {
		$fields = array();

		$step = 0;
		while (($items = $this->_query($this->_sqlAuthors($params['site_table'], $params['author_table'], $params['author_entity_field'], $step++)))&&($items->count() > 0)) {
			$lastmod = 0;
			$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><urlset></urlset>');
			$xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
			foreach ($items as $item) {
				$item['entity'] = $entity;
				$url = Yii::app()->createUrl('entity/byauthor', array(
					'entity' => Entity::GetUrlKey($entity),
					'aid' => $item['id'],
					'__useTitleParams'=>true,
					'__langForUrl'=>Yii::app()->getLanguage(),
					'title' => ProductHelper::ToAscii($item['title']))
				);
				$urlXml = $xml->addChild('url');
				$urlXml->addChild('loc', $url);
				$lastmod = max($lastmod, (int)$item['dateAdd']);
				if ($this->_isLastmod) $urlXml->addChild('lastmod', date('c', (int)$item['dateAdd']));
				if ($this->_isChangefreq) $urlXml->addChild('changefreq', 'never');
				if ($this->_isPriority) $urlXml->addChild('priority', '0.6');
			}
			unset($items);
			$fields[$this->_saveFile($xml, 'sitemap-' . $params['entity'] . '-authors-' . $step . '.xml')] = $lastmod;
		}
		return $fields;
	}

	private function _actorsXml($entity, $params) {
		$fields = array();

		$step = 0;
		while (($items = $this->_query($this->_sqlActors($params['site_table'], $step++)))&&($items->count() > 0)) {
			$lastmod = 0;
			$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><urlset></urlset>');
			$xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
			foreach ($items as $item) {
				$item['entity'] = $entity;
				$url = Yii::app()->createUrl('entity/byactor', array(
					'entity' => Entity::GetUrlKey($entity),
					'aid' => $item['id'],
					'__useTitleParams'=>true,
					'__langForUrl'=>Yii::app()->getLanguage(),
					'title' => ProductHelper::ToAscii($item['title']))
				);
				$urlXml = $xml->addChild('url');
				$urlXml->addChild('loc', $url);
				$lastmod = max($lastmod, (int)$item['dateAdd']);
				if ($this->_isLastmod) $urlXml->addChild('lastmod', date('c', (int)$item['dateAdd']));
				if ($this->_isChangefreq) $urlXml->addChild('changefreq', 'never');
				if ($this->_isPriority) $urlXml->addChild('priority', '0.6');
			}
			unset($items);
			$fields[$this->_saveFile($xml, 'sitemap-' . $params['entity'] . '-actors-' . $step . '.xml')] = $lastmod;
		}
		return $fields;
	}

	//performers
	private function _performersXml($entity, $params) {
		$fields = array();

		$step = 0;
		while (($items = $this->_query($this->_sqlPerformers($params['site_table'], $params['performer_table_list'], $params['performer_table'], $params['performer_field'], $step++)))&&($items->count() > 0)) {
			$lastmod = 0;
			$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><urlset></urlset>');
			$xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
			foreach ($items as $item) {
				$item['entity'] = $entity;
				$url = Yii::app()->createUrl('entity/byperformer', array(
					'entity' => Entity::GetUrlKey($entity),
					'pid' => $item['id'],
					'__useTitleParams'=>true,
					'__langForUrl'=>Yii::app()->getLanguage(),
					'title' => ProductHelper::ToAscii($item['title']))
				);
				$urlXml = $xml->addChild('url');
				$urlXml->addChild('loc', $url);
				$lastmod = max($lastmod, (int)$item['dateAdd']);
				if ($this->_isLastmod) $urlXml->addChild('lastmod', date('c', (int)$item['dateAdd']));
				if ($this->_isChangefreq) $urlXml->addChild('changefreq', 'never');
				if ($this->_isPriority) $urlXml->addChild('priority', '0.6');
			}
			unset($items);
			$fields[$this->_saveFile($xml, 'sitemap-' . $params['entity'] . '-performers-' . $step . '.xml')] = $lastmod;
		}
		return $fields;
	}

	private function _directorsXml($entity, $params) {
		$fields = array();

		$step = 0;
		while (($items = $this->_query($this->_sqlDirectors($params['site_table'], $step++)))&&($items->count() > 0)) {
			$lastmod = 0;
			$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><urlset></urlset>');
			$xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
			foreach ($items as $item) {
				$item['entity'] = $entity;
				$url = Yii::app()->createUrl('entity/bydirector', array(
					'entity' => Entity::GetUrlKey($entity),
					'did' => $item['id'],
					'__useTitleParams'=>true,
					'__langForUrl'=>Yii::app()->getLanguage(),
					'title' => ProductHelper::ToAscii($item['title']))
				);
				$urlXml = $xml->addChild('url');
				$urlXml->addChild('loc', $url);
				$lastmod = max($lastmod, (int)$item['dateAdd']);
				if ($this->_isLastmod) $urlXml->addChild('lastmod', date('c', (int)$item['dateAdd']));
				if ($this->_isChangefreq) $urlXml->addChild('changefreq', 'never');
				if ($this->_isPriority) $urlXml->addChild('priority', '0.6');
			}
			unset($items);
			$fields[$this->_saveFile($xml, 'sitemap-' . $params['entity'] . '-directors-' . $step . '.xml')] = $lastmod;
		}
		return $fields;
	}

	private function _bindingXml($entity, $params) {
		$fields = array();

		$items = $this->_query($this->_sqlBindings($params['site_table'], $params['binding_table']));
		if ($items->count() > 0) {
			$lastmod = 0;
			$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><urlset></urlset>');
			$xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
			foreach ($items as $item) {
				$item['entity'] = $entity;
				$url = Yii::app()->createUrl('entity/bybinding', array(
					'entity' => Entity::GetUrlKey($entity),
					'bid' => $item['id'],
					'__useTitleParams'=>true,
					'__langForUrl'=>Yii::app()->getLanguage(),
					'title' => ProductHelper::ToAscii($item['title']))
				);
				$urlXml = $xml->addChild('url');
				$urlXml->addChild('loc', $url);
				$lastmod = max($lastmod, (int)$item['dateAdd']);
				if ($this->_isLastmod) $urlXml->addChild('lastmod', date('c', (int)$item['dateAdd']));
				if ($this->_isChangefreq) $urlXml->addChild('changefreq', 'never');
				if ($this->_isPriority) $urlXml->addChild('priority', '0.6');
			}
			unset($items);
			$fields[$this->_saveFile($xml, 'sitemap-' . $params['entity'] . '-bindings.xml')] = $lastmod;
		}
		return $fields;
	}

	private function _audiostreamsXml($entity, $params) {
		$fields = array();

		$items = $this->_query($this->_sqlAudiostreams($params['site_table']));
		if ($items->count() > 0) {
			$lastmod = 0;
			$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><urlset></urlset>');
			$xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
			foreach ($items as $item) {
				$item['entity'] = $entity;
				$url = Yii::app()->createUrl('entity/byaudiostream', array(
					'entity' => Entity::GetUrlKey($entity),
					'sid' => $item['id'],
					'__useTitleParams'=>true,
					'__langForUrl'=>Yii::app()->getLanguage(),
					'title' => ProductHelper::ToAscii($item['title']))
				);
				$urlXml = $xml->addChild('url');
				$urlXml->addChild('loc', $url);
				$lastmod = max($lastmod, (int)$item['dateAdd']);
				if ($this->_isLastmod) $urlXml->addChild('lastmod', date('c', (int)$item['dateAdd']));
				if ($this->_isChangefreq) $urlXml->addChild('changefreq', 'never');
				if ($this->_isPriority) $urlXml->addChild('priority', '0.6');
			}
			unset($items);
			$fields[$this->_saveFile($xml, 'sitemap-' . $params['entity'] . '-audiostreams.xml')] = $lastmod;
		}
		return $fields;
	}

	private function _subtitlesXml($entity, $params) {
		$fields = array();

		$items = $this->_query($this->_sqlSubtitles($params['site_table']));
		if ($items->count() > 0) {
			$lastmod = 0;
			$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><urlset></urlset>');
			$xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
			foreach ($items as $item) {
				$item['entity'] = $entity;
				$url = Yii::app()->createUrl('entity/bysubtitle', array(
					'entity' => Entity::GetUrlKey($entity),
					'sid' => $item['id'],
					'__useTitleParams'=>true,
					'__langForUrl'=>Yii::app()->getLanguage(),
					'title' => ProductHelper::ToAscii($item['title']))
				);
				$urlXml = $xml->addChild('url');
				$urlXml->addChild('loc', $url);
				$lastmod = max($lastmod, (int)$item['dateAdd']);
				if ($this->_isLastmod) $urlXml->addChild('lastmod', date('c', (int)$item['dateAdd']));
				if ($this->_isChangefreq) $urlXml->addChild('changefreq', 'never');
				if ($this->_isPriority) $urlXml->addChild('priority', '0.6');
			}
			unset($items);
			$fields[$this->_saveFile($xml, 'sitemap-' . $params['entity'] . '-subtitles.xml')] = $lastmod;
		}
		return $fields;
	}

	private function _mediaXml($entity, $params) {
		$fields = array();

		$items = $this->_query($this->_sqlMedia($entity, $params['site_table']));
		if ($items->count() > 0) {
			$lastmod = 0;
			$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><urlset></urlset>');
			$xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
			foreach ($items as $item) {
				$item['entity'] = $entity;
				$url = Yii::app()->createUrl('entity/bymedia', array(
					'entity' => Entity::GetUrlKey($entity),
					'mid' => $item['id'],
					'__useTitleParams'=>true,
					'__langForUrl'=>Yii::app()->getLanguage(),
					'title' => ProductHelper::ToAscii($item['title']))
				);
				$urlXml = $xml->addChild('url');
				$urlXml->addChild('loc', $url);
				$lastmod = max($lastmod, (int)$item['dateAdd']);
				if ($this->_isLastmod) $urlXml->addChild('lastmod', date('c', (int)$item['dateAdd']));
				if ($this->_isChangefreq) $urlXml->addChild('changefreq', 'never');
				if ($this->_isPriority) $urlXml->addChild('priority', '0.6');
			}
			unset($items);
			$fields[$this->_saveFile($xml, 'sitemap-' . $params['entity'] . '-media.xml')] = $lastmod;
		}
		return $fields;
	}

	private function _magazinetypeXml($entity, $params) {
		$fields = array();

		$items = $this->_query($this->_sqlMagazinetype($params['site_table']));
		if ($items->count() > 0) {
			$lastmod = 0;
			$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><urlset></urlset>');
			$xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
			foreach ($items as $item) {
				$item['entity'] = $entity;
				$url = Yii::app()->createUrl('entity/bytype', array(
					'entity' => Entity::GetUrlKey($entity),
					'type' => $item['id'],
					'__useTitleParams'=>true,
					'__langForUrl'=>Yii::app()->getLanguage(),
					'title' => ProductHelper::ToAscii($item['title']))
				);
				$urlXml = $xml->addChild('url');
				$urlXml->addChild('loc', $url);
				$lastmod = max($lastmod, (int)$item['dateAdd']);
				if ($this->_isLastmod) $urlXml->addChild('lastmod', date('c', (int)$item['dateAdd']));
				if ($this->_isChangefreq) $urlXml->addChild('changefreq', 'never');
				if ($this->_isPriority) $urlXml->addChild('priority', '0.6');
			}
			unset($items);
			$fields[$this->_saveFile($xml, 'sitemap-' . $params['entity'] . '-magazinetype.xml')] = $lastmod;
		}
		return $fields;
	}

	private function _yearsXml($entity, $params) {
		$fields = array();

		$items = $this->_query($this->_sqlYears($params['site_table']));
		if ($items->count() > 0) {
			$lastmod = 0;
			$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><urlset></urlset>');
			$xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
			foreach ($items as $item) {
				$item['entity'] = $entity;
				$url = Yii::app()->createUrl('entity/byyear', array(
					'entity' => Entity::GetUrlKey($entity),
					'year' => $item['year'],
					'__langForUrl'=>Yii::app()->getLanguage(),
				));
				$urlXml = $xml->addChild('url');
				$urlXml->addChild('loc', $url);
				$lastmod = max($lastmod, (int)$item['dateAdd']);
				if ($this->_isLastmod) $urlXml->addChild('lastmod', date('c', (int)$item['dateAdd']));
				if ($this->_isChangefreq) $urlXml->addChild('changefreq', 'never');
				if ($this->_isPriority) $urlXml->addChild('priority', '0.6');
			}
			unset($items);
			$fields[$this->_saveFile($xml, 'sitemap-' . $params['entity'] . '-years.xml')] = $lastmod;
		}
		return $fields;
	}

	private function _sqlItems($table, $step) {
		$langs = HrefTitles::get()->getLangs('', 'product/view');
		$lang = Yii::app()->getLanguage();
		if (!in_array($lang, $langs)) $lang = 'en';
		return ''.
			'select t.id, t.title_' . $lang . ' title, UNIX_TIMESTAMP(t.last_modification_date) dateAdd '.
			'from `' . $table . '` t ' .
				'join (select id from `' . $table . '` order by id limit ' . $this->_counts*$step . ', ' . $this->_counts . ') tId using (id) '.
		'';
	}

	private function _sqlCategorys($table, $eTable) {
		$langs = HrefTitles::get()->getLangs('', 'entity/list');
		$lang = Yii::app()->getLanguage();
		if (!in_array($lang, $langs)) $lang = 'en';
		$sql = ''.
			'select t.id, t.title_' . $lang . ' title, UNIX_TIMESTAMP(max(tI.last_modification_date)) dateAdd '.
			'from `' . $eTable . '` t ' .
				'join `' . $table . '` tI on (tI.code = t.id) '.
			'group by t.id '.
		'';
		return $sql;
	}

	private function _sqlPublisher($table, $step) {
		$langs = HrefTitles::get()->getLangs('', 'entity/bypublisher');
		$lang = Yii::app()->getLanguage();
		if (!in_array($lang, $langs)) $lang = 'en';
		//на что только не пойдешь ради быстрого запроса
		//запрос странный, но работает быстро
		$sql = ''.
			'select t.id, t.title_' . $lang . ' title, UNIX_TIMESTAMP(max(tI.last_modification_date)) dateAdd '.
			'from `all_publishers` t '.
				'join (select publisher_id id '.
					'from `' . $table . '` '.
					'group by publisher_id '.
					'order by publisher_id '.
					'limit ' . $this->_counts*$step . ', ' . $this->_counts . ' '.
				') tId using (id) '.
				'left join `' . $table . '` tI on (tI.publisher_id = t.id) '.
			'group by t.id	'.
		'';
		return $sql;
	}

	private function _sqlSeries($table, $tagTable, $step) {
/**
$sql = 'SELECT tc.series_id, st.title_'.$lang.' as title FROM ' . $tbl . ' as tc, '.$series_tbl.' as st
WHERE tc.avail_for_order=1  AND (tc.series_id > 0 AND tc.series_id <> "") AND tc.series_id=st.id' .$sql.'
GROUP BY st.title_'.Yii::app()->language. (($page != 0) ? (' LIMIT ' . $limit) : '');
 */
		$langs = HrefTitles::get()->getLangs('', 'entity/byseries');
		$lang = Yii::app()->getLanguage();
		if (!in_array($lang, $langs)) $lang = 'en';
		$sql = ''.
			'select t.id, t.title_' . $lang . ' title, UNIX_TIMESTAMP(max(tI.last_modification_date)) dateAdd '.
			'from `' . $tagTable . '` t '.
				'join (select series_id id '.
					'from `' . $table . '` '.
					'where (series_id is not null) '.
					'group by series_id '.
					'order by series_id '.
					'limit ' . $this->_counts*$step . ', ' . $this->_counts . ' '.
				') tId using (id) '.
				'left join `' . $table . '` tI on (tI.series_id = t.id) '.
			'group by t.id	'.
		'';
//		echo $sql . "\n";
		return $sql;
	}

	private function _sqlAuthors($table, $tagTable, $tagField, $step) {
/**
$sql = 'SELECT ba.author_id, aa.title_'.$lang.' as title FROM ' . $tbl . ' as bc, ' . $tbl_author . ' as ba, all_authorslist as aa
WHERE avail_for_order=1  AND bc.avail_for_order=1 AND ba.' . $field . '=bc.id'.$sql.'
AND ba.author_id=aa.id
GROUP BY ba.author_id ORDER BY aa.title_'.$lang. (($page != 0) ? (' LIMIT ' . $limit) : '');
*/
		$langs = HrefTitles::get()->getLangs('', 'entity/byauthor');
		$lang = Yii::app()->getLanguage();
		if (!in_array($lang, $langs)) $lang = 'en';
		$sql = ''.
			'select t.id, t.title_' . $lang . ' title, UNIX_TIMESTAMP(max(tI.last_modification_date)) dateAdd '.
			'from `all_authorslist` t '.
				'join (select author_id id '.
					'from `' . $tagTable . '` '.
					'group by author_id '.
					'order by author_id '.
					'limit ' . $this->_counts*$step . ', ' . $this->_counts . ' '.
				') tId using (id) '.
				'left join `' . $tagTable . '` tIA on (tIA.author_id = t.id) '.
				'left join `' . $table . '` tI on (tI.id = tIA.`' . $tagField . '`) '.
			'group by t.id	'.
		'';
//		echo $sql . "\n";
		return $sql;
	}

	private function _sqlActors($table, $step) {
		$langs = HrefTitles::get()->getLangs('', 'entity/byauthor');
		$lang = Yii::app()->getLanguage();
		if (!in_array($lang, $langs)) $lang = 'en';
		$sql = ''.
			'select t.id, t.title_' . $lang . ' title, UNIX_TIMESTAMP(max(tI.last_modification_date)) dateAdd '.
			'from `all_authorslist` t '.
				'join (select person_id id '.
					'from `video_actors` '.
					'group by person_id '.
					'order by person_id '.
					'limit ' . $this->_counts*$step . ', ' . $this->_counts . ' '.
				') tId using (id) '.
				'left join `video_actors` tIA on (tIA.person_id = t.id) '.
				'left join `' . $table . '` tI on (tI.id = tIA.video_id) '.
			'group by t.id	'.
		'';
//		echo $sql . "\n";
		return $sql;
	}

	private function _sqlPerformers($table, $tableList, $tableJoin, $tagField, $step) {
		$langs = HrefTitles::get()->getLangs('', 'entity/byauthor');
		$lang = Yii::app()->getLanguage();
		if (!in_array($lang, $langs)) $lang = 'en';
		$sql = ''.
			'select t.id, t.title_' . $lang . ' title, UNIX_TIMESTAMP(max(tI.last_modification_date)) dateAdd '.
			'from `all_authorslist` t '.
				'join (select person_id id '.
					'from `' . $tableJoin . '` '.
					'group by person_id '.
					'order by person_id '.
					'limit ' . $this->_counts*$step . ', ' . $this->_counts . ' '.
				') tId using (id) '.
				'left join `' . $tableJoin . '` tIA on (tIA.person_id = t.id) '.
				'left join `' . $table . '` tI on (tI.id = tIA.' . $tagField . ') '.
			'group by t.id	'.
		'';
//		echo $sql . "\n";
		return $sql;
	}

	private function _sqlDirectors($table, $step) {
		$langs = HrefTitles::get()->getLangs('', 'entity/byauthor');
		$lang = Yii::app()->getLanguage();
		if (!in_array($lang, $langs)) $lang = 'en';
		$sql = ''.
			'select t.id, t.title_' . $lang . ' title, UNIX_TIMESTAMP(max(tI.last_modification_date)) dateAdd '.
			'from `all_authorslist` t '.
				'join (select person_id id '.
					'from `video_directors` '.
					'group by person_id '.
					'order by person_id '.
					'limit ' . $this->_counts*$step . ', ' . $this->_counts . ' '.
				') tId using (id) '.
				'left join `video_directors` tIA on (tIA.person_id = t.id) '.
				'left join `' . $table . '` tI on (tI.id = tIA.video_id) '.
			'group by t.id	'.
		'';
//		echo $sql . "\n";
		return $sql;
	}

	private function _sqlBindings($table, $tableList) {
		$langs = HrefTitles::get()->getLangs('', 'entity/bybinding');
		$lang = Yii::app()->getLanguage();
		if (!in_array($lang, $langs)) $lang = 'en';
		$sql = ''.
			'select t.id, t.title_' . $lang . ' title, UNIX_TIMESTAMP(tI.last_modification_date) dateAdd '.
			'from `' . $tableList . '` t '.
				'join ('.
					'select binding_id id, max(last_modification_date) last_modification_date '.
					'from ' . $table . ' '.
					'where (binding_id is not null) '.
					'group by binding_id'.
				') tI using (id) '.
		'';
//		echo $sql . "\n";
		return $sql;
	}

	private function _sqlAudiostreams($table) {
		$langs = HrefTitles::get()->getLangs('', 'entity/byaudiostream');
		$lang = Yii::app()->getLanguage();
		if (!in_array($lang, $langs)) $lang = 'en';
		$sql = ''.
			'select t.id, t.title_' . $lang . ' title, UNIX_TIMESTAMP(max(tI.last_modification_date)) dateAdd '.
			'from `video_audiostreamlist` t '.
				'join (select stream_id id '.
					'from `video_audiostreams` '.
					'group by stream_id '.
					'order by stream_id '.
				') tId using (id) '.
				'left join `video_audiostreams` tIA on (tIA.stream_id = t.id) '.
				'left join `' . $table . '` tI on (tI.id = tIA.video_id) '.
			'group by t.id	'.
		'';
//		echo $sql . "\n";
		return $sql;
	}

	private function _sqlSubtitles($table) {
		$langs = HrefTitles::get()->getLangs('', 'entity/bysubtitle');
		$lang = Yii::app()->getLanguage();
		if (!in_array($lang, $langs)) $lang = 'en';
		$sql = ''.
			'select t.id, t.title_' . $lang . ' title, UNIX_TIMESTAMP(max(tI.last_modification_date)) dateAdd '.
			'from `video_creditslist` t '.
				'join (select credits_id id '.
					'from `video_credits` '.
					'group by credits_id '.
					'order by credits_id '.
				') tId using (id) '.
				'left join `video_credits` tIA on (tIA.credits_id = t.id) '.
				'left join `' . $table . '` tI on (tI.id = tIA.video_id) '.
			'group by t.id	'.
		'';
//		echo $sql . "\n";
		return $sql;
	}

	private function _sqlMedia($entity, $table) {
		$sql = ''.
			'select t.id, t.title, UNIX_TIMESTAMP(tI.last_modification_date) dateAdd '.
			'from `all_media` t '.
				'join ('.
					'select media_id id, max(last_modification_date) last_modification_date '.
					'from ' . $table . ' '.
					'where (media_id is not null) and (media_id > 0) '.
					'group by media_id'.
				') tI using (id) '.
			'where (t.entity = ' . $entity . ') '.
		'';
//		echo $sql . "\n";
		return $sql;
	}

	private function _sqlMagazinetype($table) {
		$langs = HrefTitles::get()->getLangs('', 'entity/bymagazinetype');
		$lang = Yii::app()->getLanguage();
		if (!in_array($lang, $langs)) $lang = 'en';
		$sql = ''.
			'select t.id, t.title_' . $lang . ' title, UNIX_TIMESTAMP(tI.last_modification_date) dateAdd '.
			'from `pereodics_types` t '.
				'join ('.
					'select type id, max(last_modification_date) last_modification_date '.
					'from ' . $table . ' '.
					'where (type is not null) and (type > 0) '.
					'group by type'.
				') tI using (id) '.
		'';
//		echo $sql . "\n";
		return $sql;
	}

	private function _sqlYears($table) {
		//pereodics_catalog
		//
		$sql = ''.
			'select t.year, UNIX_TIMESTAMP(max(t.last_modification_date)) dateAdd '.
			'from `' . $table . '` t '.
			'where (t.year is not null) and (t.year > 0) '.
			'group by t.year '.
		'';
//		echo $sql . "\n";
		return $sql;
	}

	private function _sqlYearreleases($table) {
		//pereodics_catalog
		//
		$sql = ''.
			'select t.year, UNIX_TIMESTAMP(max(t.last_modification_date)) dateAdd '.
			'from `' . $table . '` t '.
			'where (t.year is not null) and (t.year > 0) '.
			'group by t.year '.
			'';
//		echo $sql . "\n";
		return $sql;
	}

	private function _sqlOffers() {
		$langs = array('ru', 'rut', 'en', 'fi');
		$lang = Yii::app()->getLanguage();
		if (!in_array($lang, $langs)) $lang = 'en';
		$sql = ''.
			'select id, title_' . $lang . ' title, creation_date dateAdd '.
			'from offers '.
			'where (is_active > 0) '.
			'order by id '.
		'';
		return $sql;
	}

	private function _sqlBookshelf() {
		$sql = ''.
			'select bookshelf_id id, title, date_of dateAdd '.
			'from bookshelf '.
			'where (is_visible > 0) '.
			'order by id '.
		'';
		echo $sql . "\n";
		return $sql;
	}


	private function _saveFile(SimpleXMLElement $xml, $fileName) {
		$xml->asXML($this->_dir . Yii::app()->getLanguage() . $fileName);
		return $this->_dir . Yii::app()->getLanguage() . $fileName;
	}

	private function _query($sql, $params = null) {
		require_once Yii::getPathOfAlias('webroot') . '/protected/iterators/PDO.php';
		$pdo = Yii::app()->db->createCommand($sql);
		$pdo->prepare();
		$pdo->getPdoStatement()->execute($params);
		return new IteratorsPDO($pdo->getPdoStatement());
	}

}