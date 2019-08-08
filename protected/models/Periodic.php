<?php

class Periodic extends CMyActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'pereodics_catalog';
    }

    function getEntity() { return Entity::PERIODIC; }

    public function relations()
    {
        return array(
            'type' => array(self::BELONGS_TO, 'PeriodicCategory', 'type'),
            'category' => array(self::BELONGS_TO, 'PeriodicCategory', 'code'),
            'subcategory' => array(self::BELONGS_TO, 'PeriodicCategory', 'subcode'),
            'lookinside' => array(self::HAS_MANY, 'Lookinside', 'item_id', 'on' => 'lookinside.entity='.Entity::PERIODIC ),
            'magazinetype' => array(self::BELONGS_TO, 'MagazineType', 'type'),
            'periodicCountry' => array(self::BELONGS_TO, 'PeriodicCountry', 'country'),
            'offers' => array(self::MANY_MANY, 'Offer', 'offer_items(item_id, offer_id)', 'on' => 'offers_offers.entity_id='.Entity::PERIODIC),
            'vendorData' => array(self::BELONGS_TO, 'Vendor', 'vendor'),
            'languages' => array(self::HAS_MANY, 'ItemLanguage', 'item_id', 'on' => 'languages.entity='.Entity::PERIODIC ),
        );
    }

    public static function getCountIssues($issues_year_in)
    {
        $result = [];
        $ui = Yii::app()->ui;

        $x_issues_in_year = NULL;
        $issues_year = $issues_year_in;

        if (substr($issues_year, -1, 1) == "1" &&
            substr($issues_year, -2) != "11")
        {
            $x_issues_in_year = $ui->item("X_ISSUES_IN_YEAR_1");
        }
        elseif (
            (substr($issues_year, -1, 1) == "2" && substr($issues_year, -2) != "12") ||
            (substr($issues_year, -1, 1) == "3" && substr($issues_year, -2) != "13") ||
            (substr($issues_year, -1, 1) == "4" && substr($issues_year, -2) != "14")
        )
        {
            $x_issues_in_year = $ui->item("X_ISSUES_IN_YEAR_2");
        }
        else $x_issues_in_year = $ui->item("X_ISSUES_IN_YEAR_3");

        $result['description'] = '<br/>';
        $result['description'] .= sprintf($x_issues_in_year, $issues_year) . ", ";

        $ret = array();
        if ($issues_year >= 12)
        {
            $month  = 3;
            $issues = round($issues_year_in / 12) * $month;
            $show3Months = true;
        }
        else
        {
            $j = ceil(12 / $issues_year);
            if ($j < 3)
            {
                $j = 3;
            }
            for($i = $j; $i <= 12; $i += $j) $ret[] = $i;

            $month  = $ret[0];
            $issues = $month / round(12 / $issues_year_in);
        }



        if($issues_year < 12)
        {
            $show3Months = false;
            $show6Months = false;
            $oneMonth = $issues_year / 12;
            $tmp1 = $oneMonth * 3;
            if(ctype_digit("$tmp1")) $show3Months = true;
            $tmp2 = $oneMonth * 6;
            if(ctype_digit("$tmp2")) $show6Months = true;

            if($show3Months)
            {
                $month = 3;
                $issues = $tmp1;
            }
            else if($show6Months)
            {
                $month = 6;
                $issues = $tmp2;
            }
            else
            {
                $month = 12;
                $issues = $issues_year;
            }
        }


        $issues_ending = substr($issues, -1, 1);

        if ( $issues_ending == "1" &&
            substr($issues, -2) != "11" )
        {
            $label_for_issues = $ui->item("MIN_FOR_X_MONTHS_Y_ISSUES_ISSUE_1");
        }
        elseif (
            ($issues_ending == "2" && substr($issues, -2) != "12") ||
            ($issues_ending == "3" && substr($issues, -2) != "13") ||
            ($issues_ending == "4" && substr($issues, -2) != "14")
        )
        {
            $label_for_issues = $ui->item("MIN_FOR_X_MONTHS_Y_ISSUES_ISSUE_2");
        }
        else $label_for_issues = $ui->item("MIN_FOR_X_MONTHS_Y_ISSUES_ISSUE_3");

        $month_ending = substr($month, -1, 1);

        if ( $month_ending == "1" &&
            $month != "11" )
        {
            $label_for_month = $ui->item("MIN_FOR_X_MONTHS_Y_ISSUES_MONTH_1");
        }
        elseif (
            ($month_ending == "2" && $month != "12") ||
            $month_ending == "3" ||
            $month_ending == "4"
        )
        {
            $label_for_month = $ui->item("MIN_FOR_X_MONTHS_Y_ISSUES_MONTH_2");
        }
        else $label_for_month = $ui->item("MIN_FOR_X_MONTHS_Y_ISSUES_MONTH_3");



        $msg    = sprintf
        (
            $ui->item("MIN_FOR_X_MONTHS_Y_ISSUES_TEMPLATE"),
            $month, $label_for_month,
            $issues, $label_for_issues
        );

        if ($issues_year < 12) {
            $inOneMonth = $issues_year / 12;
            $show3Months = false;
            $show6Months = false;

            $tmp1 = $inOneMonth * 3;
            if (ctype_digit("$tmp1")) $show3Months = true;

            $tmp2 = $inOneMonth * 6;
            if (ctype_digit("$tmp2")) $show6Months = true;
        }
        else {
            $show3Months = true;
            $show6Months = true;
        }


        $result['description'] .= $msg;
        $result['show3Months'] = $show3Months;
        $result['show6Months'] = $show6Months;
        $result['month'] = $month;
        $result['label_for_month'] = $label_for_month;
        $result['issues'] = $issues;
        $result['label_for_issues'] = $label_for_issues;
        $result['issues_year'] = $issues_year_in;

        return $result;
    }

    function getByCategory($cid, $exclude) {
        $dp = Entity::CreateDataProvider($this->getEntity());
        $criteria = $dp->getCriteria();
        $criteria->order = SortOptions::GetSQL(SortOptions::DefaultSort, '', $this->getEntity());
        $criteria->limit = Yii::app()->params['ItemsPerPage'];
        $criteria->offset = 0;
        $criteria->alias = 't';
        $criteria->addCondition('((t.code = ' . (int) $cid . ') or (t.subcode = ' . (int) $cid . ')) and (t.avail_for_order > 0) and (t.id not in (' . implode(',',$exclude) . '))');
        $dp->setCriteria($criteria);
        $dp->pagination = false;
        $data = $dp->getData();
        $ret = Product::FlatResult($data);
        return $ret;
    }

    function getPrices($ids) {
        if (empty($ids)) return array();

        $sql = ''.
            'select id, ' . Entity::PERIODIC . ' entity, sub_fin_year, sub_world_year, vat, discount, code, subcode, issues_year '.
            'from ' . $this->tableName() . ' '.
            'where (id in (' . implode(',', $ids) . ')) '.
        '';
        $items = array();
        foreach (Yii::app()->db->createCommand($sql)->queryAll() as $item) {
            $items[$item['id']] = $item;
            $items[$item['id']]['priceData'] = DiscountManager::GetPrice(Yii::app()->user->id, $item);
            $items[$item['id']]['priceData']['unit'] = '';
            $issues = Periodic::getCountIssues($item['issues_year']);
            if (!empty($issues['show3Months'])) {
                $items[$item['id']]['priceData']['unit'] = ' / 3 ' . Yii::app()->ui->item('MONTH_SMALL');
                $items[$item['id']]['priceData'][DiscountManager::BRUTTO] = $items[$item['id']]['priceData'][DiscountManager::BRUTTO_FIN]/4;
                $items[$item['id']]['priceData'][DiscountManager::WITH_VAT] = $items[$item['id']]['priceData'][DiscountManager::WITH_VAT_FIN]/4;
                $items[$item['id']]['priceData'][DiscountManager::WITHOUT_VAT] = $items[$item['id']]['priceData'][DiscountManager::WITHOUT_VAT_FIN]/4;
            }
            elseif (!empty($issues['show6Months'])) {
                $items[$item['id']]['priceData']['unit'] = ' / 6 ' . Yii::app()->ui->item('MONTH_SMALL');
                $items[$item['id']]['priceData'][DiscountManager::BRUTTO] = $items[$item['id']]['priceData'][DiscountManager::BRUTTO_FIN]/2;
                $items[$item['id']]['priceData'][DiscountManager::WITH_VAT] = $items[$item['id']]['priceData'][DiscountManager::WITH_VAT_FIN]/2;
                $items[$item['id']]['priceData'][DiscountManager::WITHOUT_VAT] = $items[$item['id']]['priceData'][DiscountManager::WITHOUT_VAT_FIN]/2;
            }
            else {
                $items[$item['id']]['priceData'][DiscountManager::BRUTTO] = $items[$item['id']]['priceData'][DiscountManager::BRUTTO_FIN];
                $items[$item['id']]['priceData'][DiscountManager::WITH_VAT] = $items[$item['id']]['priceData'][DiscountManager::WITH_VAT_FIN];
                $items[$item['id']]['priceData'][DiscountManager::WITHOUT_VAT] = $items[$item['id']]['priceData'][DiscountManager::WITHOUT_VAT_FIN];
                $items[$item['id']]['priceData']['unit'] = ' / 12 ' . Yii::app()->ui->item('MONTH_SMALL');
            }
        }
        return $items;
    }


}