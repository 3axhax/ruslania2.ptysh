<?php /** Created by Кирилл rkv@dfaktor.ru 16.08.2019 19:39*/

//indexer authors_boolean_mode --rotate --print-queries   для одной таблицы
/** /usr/bin/php /var/www/www-root/data/ruslania2.ptysh.ru/command.php beforesphinx
 * Class BeforeSphinxCommand
 */
require_once dirname(__FILE__) . '/BeforeSphinxCommand.php';
if (!defined('cronAction')) define('cronAction', 1);
class BeforeSphinxLiteCommand extends BeforeSphinxCommand {
    private $_counts = 10000;

    /** все авторы товара
     * @throws CDbException
     */
    protected function _fillProductsAuthors() {
        $insertSql = ''.
            'insert into _supprort_products_authors (`id`, `eid`, `name`, `position`) values(:id, :eid, :name, :pos) '.
            'on duplicate key update name=:name, position=:pos '.
        '';
        $insertPDO = Yii::app()->db->createCommand($insertSql);
        $insertPDO->prepare();
        foreach (Entity::GetEntitiesList() as $entity=>$params) {
            //$sqlItems = 'select id, title_ru, positionDefault pos from ' . $params['site_table'] . ' where (avail_for_order > 0) limit ';
            $sqlItems = 'select t.id, t.title_ru, t.positionDefault pos from ' . $params['site_table'] . ' t join _change_items tCI on (tCI.id = t.id) and (tCI.eid = ' . (int) $entity . ') limit ';
            $step = 0;
            while (($items = $this->_query($sqlItems . $this->_counts*$step . ', ' . $this->_counts))&&($items->count() > 0)) {
                $step++;
                switch (true) {
                    case Entity::checkEntityParam($entity, 'authors'):
                        foreach ($items as $item) {
                            $sql = ''.
                                'select tA.id, tA.title_ru, tA.title_rut, tA.title_en, tA.title_fi '.
                                'from all_authorslist tA '.
                                'join ' . $params['author_table'] . ' t on (t.author_id = tA.id) '.
                                'and (t.author_id > 0)'.
                                'and (t.' . $params['author_entity_field'] . ' = ' . (int)$item['id'] . ') '.
                            '';
                            $persons = $this->_query($sql);
                            $names = $this->_getAuthorNames($persons);
                            if (!empty($names)) {
                                $insertPDO->execute(array(':id'=>$item['id'], ':eid'=>$entity, ':name'=>implode(' ', $names), ':pos'=>$item['pos']));
                            }
                        }
                        break;
                    case Entity::checkEntityParam($entity, 'actors'):
                        foreach ($items as $item) {
                            $sql = ''.
                                'select tA.id, tA.title_ru, tA.title_rut, tA.title_en, tA.title_fi '.
                                'from all_authorslist tA '.
                                'join ' . $params['actors_table'] . ' t on (t.person_id = tA.id) '.
                                'and (t.person_id > 0) '.
                                'and (t.video_id = ' . (int)$item['id'] . ') '.
                            '';
                            $persons = $this->_query($sql);
                            $names = $this->_getAuthorNames($persons);
                            if (!empty($names)) {
                                $insertPDO->execute(array(':id'=>$item['id'], ':eid'=>$entity, ':name'=>implode(' ', $names), ':pos'=>$item['pos']));
                            }
                        }
                        break;
                    case Entity::checkEntityParam($entity, 'directors'):
                        foreach ($items as $item) {
                            $sql = ''.
                                'select tA.id, tA.title_ru, tA.title_rut, tA.title_en, tA.title_fi '.
                                'from all_authorslist tA '.
                                'join ' . $params['directors_table'] . ' t on (t.person_id = tA.id) '.
                                'and (t.person_id > 0) '.
                                'and (t.video_id = ' . (int)$item['id'] . ') '.
                            '';
                            $persons = $this->_query($sql);
                            $names = $this->_getAuthorNames($persons);
                            if (!empty($names)) {
                                $insertPDO->execute(array(':id'=>$item['id'], ':eid'=>$entity, ':name'=>implode(' ', $names), ':pos'=>$item['pos']));
                            }
                        }
                        break;
                    case Entity::checkEntityParam($entity, 'performers'):
                        foreach ($items as $item) {
                            $sql = ''.
                                'select tA.id, tA.title_ru, tA.title_rut, tA.title_en, tA.title_fi '.
                                'from all_authorslist tA '.
                                'join ' . $params['performer_table'] . ' t on (t.person_id = tA.id) '.
                                'and (t.person_id > 0) '.
                                'and (t.' . $params['performer_field'] . ' = ' . (int)$item['id'] . ') '.
                            '';
                            $persons = $this->_query($sql);
                            $names = $this->_getAuthorNames($persons);
                            if (!empty($names)) {
                                $insertPDO->execute(array(':id'=>$item['id'], ':eid'=>$entity, ':name'=>implode(' ', $names), ':pos'=>$item['pos']));
                            }
                        }
                        break;
                }
            }
        }

    }

    protected function _morphy() {
        echo "\n" . 'start ' . date('d.m.Y H:i:s') . "\n";

        foreach (Entity::GetEntitiesList() as $entity=>$params) {
            $fields = array(
                'title_ru', 'title_en', 'title_fi', 'title_rut', 'title_eco', //'title_original',
                'description_ru', 'description_en', 'description_fi', 'description_de',
                'description_fr', 'description_es', 'description_se', 'description_rut',
            );

            $insertPDO = null;
            $insertSql = ''.
                'insert into _morphy_' . $params['entity'] . ' (real_id, isbnnum, title, authors, description) '.
                'values(:real_id, :isbnnum, :title, :authors, :description) '.
                'on duplicate key update isbnnum = :isbnnum, title = :title, authors = :authors, description = :description '.
            '';
            $insertPDO = Yii::app()->db->createCommand($insertSql);
            $insertPDO->prepare();

            $sphynxPDO = null;
            $sphynxSql = ''.
                'insert into _sphinx_' . $params['entity'] . ' (real_id, isbnnum, ' . implode(', ', $fields) . ', authors) '.
                'values(:real_id, :isbnnum, :' . implode(', :', $fields) . ', :authors)'.
                'on duplicate key update isbnnum = :isbnnum'.
            '';
            foreach ($fields as $f) $sphynxSql .= ', ' . $f . ' = :' . $f;
            $sphynxPDO = Yii::app()->db->createCommand($sphynxSql);
            $sphynxPDO->prepare();

            $sqlItems = ''.
                'select t.id, ' . ((in_array($entity, array(30, 40)))?'""':'t.isbn') . ' isbn, '.
                't.' . implode(', t.', $fields) . ', '.
                'ifnull(tA.name, "") authors '.
                'from ' . $params['site_table'] . ' t '.
                    'left join _supprort_products_authors tA on (tA.id = t.id) and (tA.eid = ' . (int)$entity . ') '.
                    'join _change_items tCI on (tCI.id = t.id) and (tCI.eid = ' . (int)$entity . ') '.
            '';
            echo $sqlItems . "\n";
            $step = 0;
            while (($items = $this->_query($sqlItems))&&($items->count() > 0)) {
                $step++;
                foreach ($items as $item) {
/*                    $title = MorphyCommand::getMorphyNames(array('ru'=>$item['title_ru'],'en'=>$item['title_en'],'fi'=>$item['title_fi'],'rut'=>$item['title_rut'],));
                    $desc = MorphyCommand::getMorphyNames(array('ru'=>$item['description_ru'],'en'=>$item['description_en'],'fi'=>$item['description_fi'],'rut'=>$item['description_rut'],), $title);
                    $insertPDO->execute(array(
                        ':real_id'=>$item['id'],
                        ':isbnnum'=>MorphyCommand::getIsbn($item['isbn']),
                        ':title'=>implode(' ', $title),
                        ':authors'=>implode(' ', MorphyCommand::getAuthorsMorphy($item['authors'], $title)),
                        ':description'=>implode(' ', $desc),
                    ));*/


                    $authors = $this->getMorphy($item['authors']);
                    $data = array(
                        ':real_id'=>$item['id'],
                        ':isbnnum'=>MorphyCommand::getIsbn($item['isbn']),
                        ':authors'=>array(),
                    );
                    if (!empty($authors)) {
                        $data[':authors'] = array_merge($authors, $this->getMorphy(ProductHelper::ToAscii($item['authors'], array('onlyTranslite'=>true))));
                        $data[':authors'] = array_merge($data[':authors'], $this->getMorphy(ProductHelper::ToAscii(implode(' ', $authors), array('onlyTranslite'=>true))));
                    }
                    foreach ($fields as $field) {
                        if (mb_strpos($field, 'title_') === 0) {
                            $data[':' . $field] = $this->getMorphy($item[$field]);
                            if (!empty($authors)) $data[':authors'] = array_merge($data[':' . $field], $data[':authors']);
                            $data[':' . $field] = implode(' ', $data[':' . $field]);
                        }
                        else $data[':' . $field] = implode(' ', $this->getMorphy($item[$field]));
                    }
                    $data[':authors'] = array_unique($data[':authors']);
                    $data[':authors'] = implode(' ', $data[':authors']);
                    $sphynxPDO->execute($data);

                    $sql = 'delete from _change_items where (eid = ' . (int)$entity . ') and (id = ' . (int) $item['id'] . ')';
                    Yii::app()->db->createCommand($sql)->execute();
                }
                echo date('d.m.Y H:i:s') . "\n";
//			if ($step > 1) break;
            }
            echo date('d.m.Y H:i:s') . "\n";
        }


        echo 'end ' . date('d.m.Y H:i:s') . "\n";
    }

    /** при поиске авторов морфология не должна учитываться, но должны быть учитаны окончания фамилий при склонении (или множественное/единственное чило)
     * еще есть авторы в транслите
     * по этому я решил в поиске использовать словарь stem_enru, а в индекс добавлять только транслит
     * при поиске нужно так же не забыть преобразовать в транслит
     * @throws CDbException
     */
    protected function _morphyAuthors() {
        $sql = 'delete t from _morphy_authors t join _change_authors tCA on (tCA.id = t.real_id)';
        Yii::app()->db->createCommand($sql)->execute();

        $insertSql = ''.
            'insert into _morphy_authors (real_id, name, morphy_name) '.
            'values(:real_id, :name, :morphy_name) '.
        '';
        $insertPDO = Yii::app()->db->createCommand($insertSql);
        $insertPDO->prepare();
        $sqlItems = ''.
            'select t.id, t.title_ru, t.title_en, t.title_fi, t.title_rut '.
            'from all_authorslist t '.
            'join _change_authors tCA on (tCA.id = t.real_id) '.
        '';
        $step = 0;
        $sp = new SearchProducts(0);
        while (($items = $this->_query(str_replace(array('{start}', '{end}'), array($step*$this->_counts, $this->_counts), $sqlItems)))&&($items->count() > 0)) {
            $step++;
            foreach ($items as $item) {
                $name = array();
                $words = preg_split("/\W/ui", $item['title_ru'] . ' ' . $item['title_en'] . ' ' . $item['title_fi'] . ' ' . $item['title_rut']);
                $words = array_unique($words);
                foreach ($words as $k=>$v) {
                    if (is_numeric($v)) continue;
                    if (mb_strlen($v, 'utf-8') < 2) continue;
                    $name[] = $v;
                }
                $morphy = $sp->getNormalizedTransliteWord(implode(' ', $name));
                $insertPDO->execute(array(
                    ':real_id'=>$item['id'],
                    ':name'=>implode(' ', $name),
                    ':morphy_name'=>implode(' ', $morphy),
                ));
            }
            echo date('d.m.Y H:i:s') . "\n";
        }

        $sqlItems = ''.
            'select t.db_id, t.xml_value '.
            'from compliances t '.
            'join (select id from compliances where (type_id = 4) order by id limit {start}, {end}) t1 using (id) '.
            '';
        $step = 0;
        while (($items = $this->_query(str_replace(array('{start}', '{end}'), array($step*$this->_counts, $this->_counts), $sqlItems)))&&($items->count() > 0)) {
            $step++;
            foreach ($items as $item) {
                $name = array();
                $words = preg_split("/\W/ui", $item['xml_value']);
                $words = array_unique($words);
                foreach ($words as $k=>$v) {
                    if (is_numeric($v)) continue;
                    if (mb_strlen($v, 'utf-8') < 2) continue;
                    $name[] = $v;
                }
                $morphy = $sp->getNormalizedTransliteWord(implode(' ', $name));
                $insertPDO->execute(array(
                    ':real_id'=>$item['db_id'],
                    ':name'=>implode(' ', $name),
                    ':morphy_name'=>implode(' ', $morphy),
                ));
            }
            echo date('d.m.Y H:i:s') . "\n";
        }

    }

    protected function getMorphy($s) {
        if (empty($s)) return array();

        $sp = new SearchProducts(0);
        list($title, $realWords, $useRealWord) = $sp->getNormalizedWords($s);
        return $title;
    }

}
