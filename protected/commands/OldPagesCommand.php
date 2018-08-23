<?php
/*Created by Кирилл (11.07.2018 19:20)*/
ini_set('max_execution_time', 3600);
/** /usr/bin/php /var/www/www-root/data/ruslania2.ptysh.ru/command.php oldpages
 *
 *
 *
 * НЕ ЗАБЫТЬ РАСКОММЕНТИРОВАТЬ ROUTE ДЛЯ СТАРЫХ АДРЕСОВ \ruslania2.ptysh\protected\config\command-local.php
 * На всякий случай запускать 2 или даже 3 раза
 *
 *
 *
 *
 * Class OldPagesCommand
 */
define('OLD_PAGES', 1);

require_once Yii::getPathOfAlias('webroot') . '/protected/iterators/PDO.php';
class OldPagesCommand extends CConsoleCommand {
	protected $_counts = 49500; //кол-во записей за один проход

	public function actionIndex() {
		echo 'start ' . date('d.m.Y H:i:s') . "\n";
		$inswerSql = ''.
			'insert ignore into seo_redirects set '.
				'entity = :entity, '.
				'route = :route, '.
				'id = :id, '.
				'path = :path, '.
				'lang = :lang '.
		'';
		$pdo = Yii::app()->db->createCommand($inswerSql);
		$pdo->prepare();
		foreach (Entity::GetEntitiesList() as $entity=>$params) {
			$this->_itemPages($entity, $params, $pdo);
			$this->_categoryPages($entity, $params, $pdo);
			$this->_tagPages($entity, $params, $pdo);
		}
		$this->_staticPages($pdo);
		$this->_offerPages($pdo);
		$this->_bookshelfPages($pdo);
		echo 'end ' . date('d.m.Y H:i:s') . "\n\n";

	}

	private function _categoryPages($entity, $params, CDbCommand $pdo) {
//,
		$langs = array('ru', 'rut', 'en', 'fi', 'de', 'fr', 'es', 'se');
		$step = 0;
		$items = $this->_query($this->_sqlCategorys($entity, $params['site_category_table'], $step++));
//		while (($items = $this->_query($this->_sqlCategorys($entity, $params['site_category_table'], $step++)))&&(($itemCounts = $items->count()) > 0)) {
			foreach ($items as $item) {
				foreach ($langs as $lang) {
					$urlParams = array(
						'entity' => Entity::GetUrlKey($entity),
						'cid' => $item['id'],
						'title'=>ProductHelper::ToAscii($item['title_' . $lang])
					);
					$insertParams = array(
						':entity'=>$entity,
						':route'=>'entity/list',
						':id'=>$item['id'],
						':path'=>Yii::app()->createUrl('entity/list', $urlParams),
						':lang'=>$lang,
					);
					$pdo->getPdoStatement()->execute($insertParams);
				}
			}
//			unset($items);
			echo $params['site_category_table'] . ' ' . (($step-1)*$this->_counts + count($items)) . "\n";
//			if ($itemCounts < $this->_counts) break;
//		}
		$urlParams = array(
			'entity' => Entity::GetUrlKey($entity),
		);
		foreach ($langs as $lang) {
			$insertParams = array(
				':entity' => $entity,
				':route' => 'entity/list',
				':id' => 0,
				':path' => Yii::app()->createUrl('entity/list', $urlParams),
				':lang' => $lang,
			);
			$pdo->getPdoStatement()->execute($insertParams);
		}
		$insertParams = array(
			':entity' => $entity,
			':route' => 'entity/categorylist',
			':id' => 0,
			':path' => Yii::app()->createUrl('entity/categorylist', $urlParams),
			':lang' => 'ru',
		);
		$pdo->getPdoStatement()->execute($insertParams);

	}

	private function _itemPages($entity, $params, CDbCommand $pdo) {
		$step = 0;
		$langs = array('ru', 'rut', 'en', 'fi');
		while (($items = $this->_query($this->_sqlItems($entity, $params['site_table'], $step++)))&&(($itemCounts = $items->count()) > 0)) {
			foreach ($items as $item) {
				$item['entity'] = $entity;
				foreach ($langs as $lang) {
					$insertParams = array(
						':entity'=>$entity,
						':route'=>'product/view',
						':id'=>$item['id'],
						':path'=>ProductHelper::CreateUrl($item, $lang),
						':lang'=>$lang,
					);
					$pdo->getPdoStatement()->execute($insertParams);
				}
			}
			unset($items);
			echo $params['site_table'] . ' ' . (($step-1)*$this->_counts + $itemCounts) . "\n";
//			if ($itemCounts < $this->_counts) break;
		}
	}

	private function _tagPages($entity, $params, CDbCommand $pdo) {
		$smapHtml = new Sitemap();
		list($tags, $tagsAll, $tagsHand) = $smapHtml->getTags();
		foreach ($tags as $tag=>$param) {
			$funcName = '_' . $tag . 'Pages';
			if ($smapHtml->checkTagByEntity($tag, $entity)&&method_exists($this, $funcName)) {
				$this->$funcName($entity, $params, $pdo);
			}
		}
		foreach ($tagsAll as $tag=>$param) {
			$funcName = '_' . $tag . 'Pages';
			if ($smapHtml->checkTagByEntity($tag, $entity)&&method_exists($this, $funcName)) {
				$this->$funcName($entity, $params, $pdo);
			}
		}

	}

	private function _bookshelfPages(CDbCommand $pdo) {
		$insertParams = array(
			':entity'=>0,
			':route'=>'bookshelf/list',
			':id'=>0,
			':path'=>Yii::app()->createUrl('bookshelf/list'),
			':lang'=>'ru',
		);
		$pdo->getPdoStatement()->execute($insertParams);
		$items = $this->_query($this->_sqlBookshelf());
		foreach ($items as $item) {
			$insertParams = array(
				':entity'=>0,
				':route'=>'bookshelf/view',
				':id'=>$item['id'],
				':path'=>Yii::app()->createUrl('bookshelf/view', array('id'=>$item['id'])),
				':lang'=>'ru',
			);
			$pdo->getPdoStatement()->execute($insertParams);
		}
		echo "bookshelf\n";
	}

	private function _offerPages(CDbCommand $pdo) {
		$insertParams = array(
			':entity'=>0,
			':route'=>'offers/list',
			':id'=>0,
			':path'=>Yii::app()->createUrl('offers/list'),
			':lang'=>'ru',
		);
		$pdo->getPdoStatement()->execute($insertParams);

		$const = array(
			Offer::FIRMS => 'firms',
			Offer::LIBRARY => 'lib',
			Offer::UNI => 'uni',
			Offer::FREE_SHIPPING => 'fs',
			Offer::ALLE_2_EURO => 'alle2',
		);

		foreach ($const as $id=>$name) {
			$insertParams = array(
				':entity'=>0,
				':route'=>'offers/special',
				':id'=>0,
				':path'=>Yii::app()->createUrl('offers/special', array('mode' => $const[$id])),
				':lang'=>'ru',
			);
			$pdo->getPdoStatement()->execute($insertParams);
		}

		$langs = array('ru', 'en', 'fi');
		$items = $this->_query($this->_sqlOffers());
		foreach ($items as $item) {
			foreach ($langs as $lang) {
				$insertParams = array(
					':entity'=>0,
					':route'=>'offers/view',
					':id'=>$item['id'],
					':path'=>Yii::app()->createUrl('offers/view', array('oid'=>$item['id'], 'title'=>ProductHelper::ToAscii($item['title_' . $lang]))),
					':lang'=>$lang,
				);
				$pdo->getPdoStatement()->execute($insertParams);
			}
		}
		unset($items);
		echo "offers\n";
	}

	private function _staticPages(CDbCommand $pdo) {
		foreach (StaticUrlRule::getTitles() as $pageName=>$name) {
			$insertParams = array(
				':entity'=>0,
				':route'=>'site/static',
				':id'=>0,
				':path'=>Yii::app()->createUrl('site/static', array('page'=>$pageName)),
				':lang'=>'ru',
			);
			$pdo->getPdoStatement()->execute($insertParams);
		}
		$smapHtml = new Sitemap();
		foreach ($smapHtml->getStaticPages() as $pageName=>$param) {
			$insertParams = array(
				':entity'=>0,
				':route'=>$param['route'],
				':id'=>0,
				':path'=>Yii::app()->createUrl($param['route']),
				':lang'=>'ru',
			);
			$pdo->getPdoStatement()->execute($insertParams);
		}
		echo "static\n";
	}

	private function _query($sql, $params = null) {
		$pdo = Yii::app()->db->createCommand($sql);
		$pdo->prepare();
		$pdo->getPdoStatement()->execute($params);
		return new IteratorsPDO($pdo->getPdoStatement());
	}

	private function _sqlItems($entity, $table, $step) {
		return ''.
		'select t.id, t.title_ru, title_rut, title_en, title_fi '.
		'from `' . $table . '` t ' .
			'join ('.
				'select tI.id '.
				'from `' . $table . '` tI '.
					'left join seo_redirects tSR on (tSR.id = tI.id) and (tSR.entity = ' . (int) $entity . ') and (tSR.route = "product/view") '.
				'where (tSR.id is null) '.
				'order by tI.id '.
				'limit ' . $this->_counts*$step . ', ' . $this->_counts . ''.
			') tId using (id) '.
		'';
	}

	private function _sqlBookshelf() {
		return ''.
			'select t.bookshelf_id id, t.title '.
			'from `bookshelf` t ' .
			'where (t.is_visible = 1) '.
		'';
	}

	private function _sqlOffers() {
		return ''.
			'select t.id, t.title_ru, title_en, title_fi '.
			'from `offers` t ' .
			'where (t.is_active = 1) '.
				'and (t.is_special = 0) '.
		'';
	}

	private function _sqlCategorys($entity, $table, $step) {
		return ''.
			'select t.id, t.title_ru, t.title_rut, t.title_en, t.title_fi, t.title_de, t.title_fr, t.title_es, t.title_se '.
			'from `' . $table . '` t ' .
//				'join ('.
//					'select tI.id '.
//					'from `' . $table . '` tI '.
						//'left join seo_redirects tSR on (tSR.id = tI.id) and (tSR.entity = ' . (int) $entity . ') and (tSR.route = "entity/list") '.
//					'where (tSR.id is null) '.
//					'order by tI.id '.
//					'limit ' . $this->_counts*$step . ', ' . $this->_counts . ''.
//				') tId using (id) '.
		'union all '.
			'select t.id, t.title_en title_ru, t.title_en title_rut, t.title_en, t.title_en title_fi, t.title_en title_de, t.title_en title_fr, t.title_en title_es, t.title_en title_se '.
			'from `' . $table . '` t ' .
		'';
	}

	private function _publisherPages($entity, $params, CDbCommand $pdo) {
		$langs = array('ru', 'en');
		$step = 0;
		while (($items = $this->_query($this->_sqlPublisher($entity, $step++)))&&(($itemCounts = $items->count()) > 0)) {
			foreach ($items as $item) {
				foreach ($langs as $lang) {
					$urlParams = array(
						'entity' => Entity::GetUrlKey($entity),
						'pid' => $item['id'],
						'title'=>ProductHelper::ToAscii($item['title_' . $lang])
					);
					$insertParams = array(
						':entity'=>$entity,
						':route'=>'entity/bypublisher',
						':id'=>$item['id'],
						':path'=>Yii::app()->createUrl('entity/bypublisher', $urlParams),
						':lang'=>$lang,
					);
					$pdo->getPdoStatement()->execute($insertParams);
				}
			}
			unset($items);
			echo 'all_publishers ' . ' bypublisher ' . $entity . ' ' . (($step-1)*$this->_counts + $itemCounts) . "\n";
//			if ($itemCounts < $this->_counts) break;
		}
		foreach ($langs as $lang) {
			$urlParams = array(
				'entity' => Entity::GetUrlKey($entity),
			);
			$insertParams = array(
				':entity'=>$entity,
				':route'=>'entity/publisherlist',
				':id'=>0,
				':path'=>Yii::app()->createUrl('entity/publisherlist', $urlParams),
				':lang'=>$lang,
			);
			$pdo->getPdoStatement()->execute($insertParams);
		}

	}

	private function _yearsPages($entity, $params, CDbCommand $pdo) {
		$langs = array('ru', 'en');
		$items = $this->_query($this->_sqlYears($entity, $params['site_table']));
		foreach ($items as $item) {
			foreach ($langs as $lang) {
				$urlParams = array(
					'entity' => Entity::GetUrlKey($entity),
					'year' => $item['year'],
				);
				$insertParams = array(
					':entity'=>$entity,
					':route'=>'entity/byyear',
					':id'=>$item['year'],
					':path'=>Yii::app()->createUrl('entity/byyear', $urlParams),
					':lang'=>$lang,
				);
				$pdo->getPdoStatement()->execute($insertParams);
			}
		}
		unset($items);
		echo 'all_years ' . ' byyear ' . $entity . "\n";
		$urlParams = array(
			'entity' => Entity::GetUrlKey($entity),
		);
		$insertParams = array(
			':entity'=>$entity,
			':route'=>'entity/yearslist',
			':id'=>0,
			':path'=>Yii::app()->createUrl('entity/yearslist', $urlParams),
			':lang'=>'ru',
		);
		$pdo->getPdoStatement()->execute($insertParams);

	}

	private function _seriesPages($entity, $params, CDbCommand $pdo) {
		$langs = array('ru', 'rut', 'en', 'fi');
		$step = 0;
		while (($items = $this->_query($this->_sqlSeries($entity, $params['site_series_table'], $step++, $langs)))&&(($itemCounts = $items->count()) > 0)) {
			foreach ($items as $item) {
				foreach ($langs as $lang) {
					$urlParams = array(
						'entity' => Entity::GetUrlKey($entity),
						'sid' => $item['id'],
						'title'=>ProductHelper::ToAscii($item['title_' . $lang])
					);
					$insertParams = array(
						':entity'=>$entity,
						':route'=>'entity/byseries',
						':id'=>$item['id'],
						':path'=>Yii::app()->createUrl('entity/byseries', $urlParams),
						':lang'=>$lang,
					);
					$pdo->getPdoStatement()->execute($insertParams);
				}
			}
			unset($items);
			echo $params['site_series_table'] . ' byseries ' . $entity . ' ' . (($step-1)*$this->_counts + $itemCounts) . "\n";
//			if ($itemCounts < $this->_counts) break;
		}
		foreach ($langs as $lang) {
			$urlParams = array(
				'entity' => Entity::GetUrlKey($entity),
			);
			$insertParams = array(
				':entity'=>$entity,
				':route'=>'entity/serieslist',
				':id'=>0,
				':path'=>Yii::app()->createUrl('entity/serieslist', $urlParams),
				':lang'=>$lang,
			);
			$pdo->getPdoStatement()->execute($insertParams);
		}
	}

	private function _authorsPages($entity, $params, CDbCommand $pdo) {
		$langs = array('ru', 'rut', 'en', 'fi');
		$step = 0;
		while (($items = $this->_query($this->_sqlAuthors($entity, $params['author_table'], $step++, $langs)))&&(($itemCounts = $items->count()) > 0)) {
			foreach ($items as $item) {
				foreach ($langs as $lang) {
					$urlParams = array(
						'entity' => Entity::GetUrlKey($entity),
						'aid' => $item['id'],
						'title'=>ProductHelper::ToAscii($item['title_' . $lang])
					);
					$insertParams = array(
						':entity'=>$entity,
						':route'=>'entity/byauthor',
						':id'=>$item['id'],
						':path'=>Yii::app()->createUrl('entity/byauthor', $urlParams),
						':lang'=>$lang,
					);
					$pdo->getPdoStatement()->execute($insertParams);
				}
			}
			unset($items);
			echo $params['author_table'] . ' byauthor ' . $entity . ' ' . ' ' . (($step-1)*$this->_counts + $itemCounts) . "\n";
//			if ($itemCounts < $this->_counts) break;
		}
		foreach ($langs as $lang) {
			$urlParams = array(
				'entity' => Entity::GetUrlKey($entity),
			);
			$insertParams = array(
				':entity'=>$entity,
				':route'=>'entity/authorlist',
				':id'=>0,
				':path'=>Yii::app()->createUrl('entity/authorlist', $urlParams),
				':lang'=>$lang,
			);
			$pdo->getPdoStatement()->execute($insertParams);
		}
	}

	private function _actorsPages($entity, $params, CDbCommand $pdo) {
		$langs = array('ru', 'rut', 'en', 'fi');
		$step = 0;
		while (($items = $this->_query($this->_sqlActors($entity, $step++, $langs)))&&(($itemCounts = $items->count()) > 0)) {
			foreach ($items as $item) {
				foreach ($langs as $lang) {
					$urlParams = array(
						'entity' => Entity::GetUrlKey($entity),
						'aid' => $item['id'],
						'title'=>ProductHelper::ToAscii($item['title_' . $lang])
					);
					$insertParams = array(
						':entity'=>$entity,
						':route'=>'entity/byactor',
						':id'=>$item['id'],
						':path'=>Yii::app()->createUrl('entity/byactor', $urlParams),
						':lang'=>$lang,
					);
					$pdo->getPdoStatement()->execute($insertParams);
				}
			}
			unset($items);
			echo 'video_actors ' . ' byactor ' . $entity . ' '  . (($step-1)*$this->_counts + $itemCounts) . "\n";
//			if ($itemCounts < $this->_counts) break;
		}
		foreach ($langs as $lang) {
			$urlParams = array(
				'entity' => Entity::GetUrlKey($entity),
			);
			$insertParams = array(
				':entity'=>$entity,
				':route'=>'entity/actorlist',
				':id'=>0,
				':path'=>Yii::app()->createUrl('entity/actorlist', $urlParams),
				':lang'=>$lang,
			);
			$pdo->getPdoStatement()->execute($insertParams);
		}
	}

	private function _performersPages($entity, $params, CDbCommand $pdo) {
		$langs = array('ru', 'rut', 'en', 'fi');
		$step = 0;
		while (($items = $this->_query($this->_sqlPerformers($entity, $params['performer_table'], $step++, $langs)))&&(($itemCounts = $items->count()) > 0)) {
			foreach ($items as $item) {
				foreach ($langs as $lang) {
					$urlParams = array(
						'entity' => Entity::GetUrlKey($entity),
						'pid' => $item['id'],
						'title'=>ProductHelper::ToAscii($item['title_' . $lang])
					);
					$insertParams = array(
						':entity'=>$entity,
						':route'=>'entity/byperformer',
						':id'=>$item['id'],
						':path'=>Yii::app()->createUrl('entity/byperformer', $urlParams),
						':lang'=>$lang,
					);
					$pdo->getPdoStatement()->execute($insertParams);
				}
			}
			unset($items);
			echo $params['performer_table'] . ' byperformer ' . $entity . ' ' . ' ' . (($step-1)*$this->_counts + $itemCounts) . "\n";
//			if ($itemCounts < $this->_counts) break;
		}
		foreach ($langs as $lang) {
			$urlParams = array(
				'entity' => Entity::GetUrlKey($entity),
			);
			$insertParams = array(
				':entity'=>$entity,
				':route'=>'entity/performerlist',
				':id'=>0,
				':path'=>Yii::app()->createUrl('entity/performerlist', $urlParams),
				':lang'=>$lang,
			);
			$pdo->getPdoStatement()->execute($insertParams);
		}
	}

	private function _directorsPages($entity, $params, CDbCommand $pdo) {
		$langs = array('ru', 'rut', 'en', 'fi');
		$step = 0;
		while (($items = $this->_query($this->_sqlDirectors($entity, $step++, $langs)))&&(($itemCounts = $items->count()) > 0)) {
			foreach ($items as $item) {
				foreach ($langs as $lang) {
					$urlParams = array(
						'entity' => Entity::GetUrlKey($entity),
						'did' => $item['id'],
						'title'=>ProductHelper::ToAscii($item['title_' . $lang])
					);
					$insertParams = array(
						':entity'=>$entity,
						':route'=>'entity/bydirector',
						':id'=>$item['id'],
						':path'=>Yii::app()->createUrl('entity/bydirector', $urlParams),
						':lang'=>$lang,
					);
					$pdo->getPdoStatement()->execute($insertParams);
				}
			}
			unset($items);
			echo 'video_directors ' . ' bydirector ' . $entity . ' ' . (($step-1)*$this->_counts + $itemCounts) . "\n";
//			if ($itemCounts < $this->_counts) break;
		}
		foreach ($langs as $lang) {
			$urlParams = array(
				'entity' => Entity::GetUrlKey($entity),
			);
			$insertParams = array(
				':entity'=>$entity,
				':route'=>'entity/directorlist',
				':id'=>0,
				':path'=>Yii::app()->createUrl('entity/directorlist', $urlParams),
				':lang'=>$lang,
			);
			$pdo->getPdoStatement()->execute($insertParams);
		}
	}

	private function _bindingPages($entity, $params, CDbCommand $pdo) {
		$langs = array('ru', 'rut', 'en', 'fi');
		$step = 0;
		$items = $this->_query($this->_sqlBindings($entity, $params['binding_table'], $langs));
		foreach ($items as $item) {
			foreach ($langs as $lang) {
				$urlParams = array(
					'entity' => Entity::GetUrlKey($entity),
					'bid' => $item['id'],
					'title'=>ProductHelper::ToAscii($item['title_' . $lang])
				);
				$insertParams = array(
					':entity'=>$entity,
					':route'=>'entity/bybinding',
					':id'=>$item['id'],
					':path'=>Yii::app()->createUrl('entity/bybinding', $urlParams),
					':lang'=>$lang,
				);
				$pdo->getPdoStatement()->execute($insertParams);
			}
		}
		unset($items);
		echo $params['binding_table'] . ' bybinding ' . $entity . ' ' . "\n";
		foreach ($langs as $lang) {
			$urlParams = array(
				'entity' => Entity::GetUrlKey($entity),
			);
			$insertParams = array(
				':entity'=>$entity,
				':route'=>'entity/bindingslist',
				':id'=>0,
				':path'=>Yii::app()->createUrl('entity/bindingslist', $urlParams),
				':lang'=>$lang,
			);
			$pdo->getPdoStatement()->execute($insertParams);
		}
	}

	private function _audiostreamsPages($entity, $params, CDbCommand $pdo) {
		$langs = array('ru', 'rut', 'en', 'fi', 'de', 'fr', 'it', 'es', 'se');
		$items = $this->_query($this->_sqlAudiostreams($entity, $langs));
		foreach ($items as $item) {
			foreach ($langs as $lang) {
				$urlParams = array(
					'entity' => Entity::GetUrlKey($entity),
					'sid' => $item['id'],
					'title'=>ProductHelper::ToAscii($item['title_' . $lang])
				);
				$insertParams = array(
					':entity'=>$entity,
					':route'=>'entity/byaudiostream',
					':id'=>$item['id'],
					':path'=>Yii::app()->createUrl('entity/byaudiostream', $urlParams),
					':lang'=>$lang,
				);
				$pdo->getPdoStatement()->execute($insertParams);
			}
		}
		unset($items);
		echo "video_audiostreamlist\n";
		foreach ($langs as $lang) {
			$urlParams = array(
				'entity' => Entity::GetUrlKey($entity),
			);
			$insertParams = array(
				':entity'=>$entity,
				':route'=>'entity/audiostreamslist',
				':id'=>0,
				':path'=>Yii::app()->createUrl('entity/audiostreamslist', $urlParams),
				':lang'=>$lang,
			);
			$pdo->getPdoStatement()->execute($insertParams);
		}
	}

	private function _subtitlesPages($entity, $params, CDbCommand $pdo) {
		$langs = array('ru', 'rut', 'en', 'fi', 'de', 'fr', 'it', 'es', 'se');
		$items = $this->_query($this->_sqlSubtitles($entity, $langs));
		foreach ($items as $item) {
			foreach ($langs as $lang) {
				$urlParams = array(
					'entity' => Entity::GetUrlKey($entity),
					'sid' => $item['id'],
					'title'=>ProductHelper::ToAscii($item['title_' . $lang])
				);
				$insertParams = array(
					':entity'=>$entity,
					':route'=>'entity/bysubtitle',
					':id'=>$item['id'],
					':path'=>Yii::app()->createUrl('entity/bysubtitle', $urlParams),
					':lang'=>$lang,
				);
				$pdo->getPdoStatement()->execute($insertParams);
			}
		}
		unset($items);
		echo "video_creditslist\n";
		foreach ($langs as $lang) {
			$urlParams = array(
				'entity' => Entity::GetUrlKey($entity),
			);
			$insertParams = array(
				':entity'=>$entity,
				':route'=>'entity/subtitleslist',
				':id'=>0,
				':path'=>Yii::app()->createUrl('entity/subtitleslist', $urlParams),
				':lang'=>$lang,
			);
			$pdo->getPdoStatement()->execute($insertParams);
		}
	}

	private function _mediaPages($entity, $params, CDbCommand $pdo) {
		$items = $this->_query($this->_sqlMedia($entity));
		foreach ($items as $item) {
			$urlParams = array(
				'entity' => Entity::GetUrlKey($entity),
				'mid' => $item['id'],
				'title'=>ProductHelper::ToAscii($item['title'])
			);
			$insertParams = array(
				':entity'=>$entity,
				':route'=>'entity/bymedia',
				':id'=>$item['id'],
				':path'=>Yii::app()->createUrl('entity/bymedia', $urlParams),
				':lang'=>'ru',
			);
			$pdo->getPdoStatement()->execute($insertParams);
		}
		unset($items);
		echo "all_media\n";
		$urlParams = array(
			'entity' => Entity::GetUrlKey($entity),
		);
		$insertParams = array(
			':entity'=>$entity,
			':route'=>'entity/medialist',
			':id'=>0,
			':path'=>Yii::app()->createUrl('entity/medialist', $urlParams),
			':lang'=>'ru',
		);
		$pdo->getPdoStatement()->execute($insertParams);
	}

	private function _magazinetypePages($entity, $params, CDbCommand $pdo) {
		$langs = array('ru', 'rut', 'en', 'fi');
		$items = $this->_query($this->_sqlMagazinetype($entity, $langs));
		foreach ($items as $item) {
			foreach ($langs as $lang) {
				$urlParams = array(
					'entity' => Entity::GetUrlKey($entity),
					'tid' => $item['id'],
					'title'=>ProductHelper::ToAscii($item['title_' . $lang])
				);
				$insertParams = array(
					':entity'=>$entity,
					':route'=>'entity/bymagazinetype',
					':id'=>$item['id'],
					':path'=>Yii::app()->createUrl('entity/bymagazinetype', $urlParams),
					':lang'=>$lang,
				);
				$pdo->getPdoStatement()->execute($insertParams);
			}
		}
		unset($items);
		echo "pereodics_types\n";
		foreach ($langs as $lang) {
			$urlParams = array(
				'entity' => Entity::GetUrlKey($entity),
			);
			$insertParams = array(
				':entity'=>$entity,
				':route'=>'entity/typeslist',
				':id'=>0,
				':path'=>Yii::app()->createUrl('entity/typeslist', $urlParams),
				':lang'=>$lang,
			);
			$pdo->getPdoStatement()->execute($insertParams);
		}
	}


	private function _sqlPublisher($entity, $step) {
		$sql = ''.
			'select t.id, t.title_ru, t.title_en '.
			'from `all_publishers` t '.
				'join (select tI.id '.
					'from `all_publishers` tI '.
						'left join seo_redirects tSR on (tSR.id = tI.id) and (tSR.entity = ' . (int) $entity . ') and (tSR.route = "entity/bypublisher") '.
					'where (tSR.id is null) '.
					'order by tI.id '.
					'limit ' . $this->_counts*$step . ', ' . $this->_counts . ' '.
				') tId using (id) '.
		'';
		return $sql;
	}

	private function _sqlYears($entity, $table) {
		$sql = ''.
			'select t.year, UNIX_TIMESTAMP(max(t.last_modification_date)) dateAdd '.
			'from `' . $table . '` t '.
				'left join seo_redirects tSR on (tSR.id = t.id) and (tSR.entity = ' . (int) $entity . ') and (tSR.route = "entity/byyear") '.
			'where (t.year is not null) and (t.year > 0) and (tSR.id is null) '.
			'group by t.year '.
			'';
		return $sql;
	}

	private function _sqlSeries($entity, $tagTable, $step, $langs) {
		$sql = ''.
			'select t.id, t.title_' . implode(', t.title_', $langs) . ' '.
			'from `' . $tagTable . '` t '.
				'join (select tI.id '.
					'from `' . $tagTable . '` tI '.
						'left join seo_redirects tSR on (tSR.id = tI.id) and (tSR.entity = ' . (int) $entity . ') and (tSR.route = "entity/byseries") '.
					'where (tSR.id is null) '.
					'order by tI.id '.
					'limit ' . $this->_counts*$step . ', ' . $this->_counts . ' '.
				') tId using (id) '.
		'';
		return $sql;
	}

	private function _sqlAuthors($entity, $tagTable, $step, $langs) {
		$sql = ''.
			'select t.id, t.title_' . implode(', t.title_', $langs) . ' '.
			'from `all_authorslist` t '.
				'join (select tI.author_id id '.
					'from `' . $tagTable . '` tI '.
						'left join seo_redirects tSR on (tSR.id = tI.author_id) and (tSR.entity = ' . (int) $entity . ') and (tSR.route = "entity/byauthor") '.
					'where (tSR.id is null) '.
					'group by tI.author_id '.
					'order by tI.author_id '.
					'limit ' . $this->_counts*$step . ', ' . $this->_counts . ' '.
				') tId using (id) '.
		'';
//		echo $sql . "\n";
		return $sql;
	}

	private function _sqlActors($entity, $step, $langs) {
		$sql = ''.
			'select t.id, t.title_' . implode(', t.title_', $langs) . ' '.
			'from `all_authorslist` t '.
				'join (select tI.person_id id '.
					'from `video_actors` tI '.
						'left join seo_redirects tSR on (tSR.id = tI.person_id) and (tSR.entity = ' . (int) $entity . ') and (tSR.route = "entity/byactor") '.
					'where (tSR.id is null) '.
					'group by tI.person_id '.
					'order by tI.person_id '.
					'limit ' . $this->_counts*$step . ', ' . $this->_counts . ' '.
				') tId using (id) '.
		'';
		return $sql;
	}

	private function _sqlPerformers($entity, $tagTable, $step, $langs) {
		$sql = ''.
			'select t.id, t.title_' . implode(', t.title_', $langs) . ' '.
			'from `all_authorslist` t '.
				'join (select tI.person_id id '.
					'from `' . $tagTable . '` tI '.
						'left join seo_redirects tSR on (tSR.id = tI.person_id) and (tSR.entity = ' . (int) $entity . ') and (tSR.route = "entity/byperformer") '.
						'where (tSR.id is null) '.
					'group by tI.person_id '.
					'order by tI.person_id '.
					'limit ' . $this->_counts*$step . ', ' . $this->_counts . ' '.
				') tId using (id) '.
		'';
		return $sql;
	}

	private function _sqlDirectors($entity, $step, $langs) {
		$sql = ''.
			'select t.id, t.title_' . implode(', t.title_', $langs) . ' '.
			'from `all_authorslist` t '.
				'join (select tI.person_id id '.
					'from `video_directors` tI '.
						'left join seo_redirects tSR on (tSR.id = tI.person_id) and (tSR.entity = ' . (int) $entity . ') and (tSR.route = "entity/bydirector") '.
					'where (tSR.id is null) '.
					'group by tI.person_id '.
					'order by tI.person_id '.
					'limit ' . $this->_counts*$step . ', ' . $this->_counts . ' '.
				') tId using (id) '.
		'';
		return $sql;
	}

	private function _sqlBindings($entity, $tableList, $langs) {
		$sql = ''.
			'select t.id, t.title_' . implode(', t.title_', $langs) . ' '.
			'from `' . $tableList . '` t '.
				'left join seo_redirects tSR on (tSR.id = t.id) and (tSR.entity = ' . (int) $entity . ') and (tSR.route = "entity/bybinding") '.
			'where (tSR.id is null) '.
		'';
		return $sql;
	}

	private function _sqlAudiostreams($entity, $langs) {
		$sql = ''.
			'select t.id, t.title_' . implode(', t.title_', $langs) . ' '.
			'from `video_audiostreamlist` t '.
				'left join seo_redirects tSR on (tSR.id = t.id) and (tSR.entity = ' . (int) $entity . ') and (tSR.route = "entity/byaudiostream") '.
			'where (tSR.id is null) '.
		'';
		return $sql;
	}

	private function _sqlSubtitles($entity, $langs) {
		$sql = ''.
			'select t.id, t.title_' . implode(', t.title_', $langs) . ' '.
			'from `video_creditslist` t '.
				'left join seo_redirects tSR on (tSR.id = t.id) and (tSR.entity = ' . (int) $entity . ') and (tSR.route = "entity/bysubtitle") '.
			'where (tSR.id is null) '.
		'';
		return $sql;
	}

	private function _sqlMedia($entity) {
		$sql = ''.
			'select t.id, t.title '.
			'from `all_media` t '.
				'left join seo_redirects tSR on (tSR.id = t.id) and (tSR.entity = ' . (int) $entity . ') and (tSR.route = "entity/bymedia") '.
			'where (tSR.id is null) '.
		'';
		return $sql;
	}

	private function _sqlMagazinetype($entity, $langs) {
		$sql = ''.
			'select t.id, t.title_' . implode(', t.title_', $langs) . ' '.
			'from `pereodics_types` t '.
				'left join seo_redirects tSR on (tSR.id = t.id) and (tSR.entity = ' . (int) $entity . ') and (tSR.route = "entity/bymagazinetype") '.
			'where (tSR.id is null) '.
		'';
		return $sql;
	}

}
