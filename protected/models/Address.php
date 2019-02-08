<?php

class Address extends CActiveRecord
{
    const ORGANIZATION = 1;
    const PRIVATE_PERSON = 2;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Никто не платит налог, кроме
    1) все в финляндии, частники и фирмы  (код предприятии не сбрасывает налог)
    2) частники в еврозоне и фирмы в еврозоне, которые не указали код предприятии.

    фирмы в еврозоне, которые указывают европейский код предприятии, не платят налог (он должен сбраживаться при указании кода предприятии).

    Фирмы вне еврозоны тоже могут указывать какой-то номер, но он в процессе ничего не делает.

    НДС всегда считается по стране доставки.
    И так, если белорусский клиент плательщик, но доставка в Германию, то налог есть.
    Если доставка в Беларусь, налога нет.

    А если адрес доставки в Германии фирме которая указала код предприятии, то налога нет.
     * @param $address
     * @return bool
     */
    public static function UseVAT($address)
    {
        $withVat = true;
        if(empty($address)) return true;

        $isCanarie = ($address['code'] == 'ES')
                     &&
            ((isset($address['country_name']) && $address['country_name'] == 'Spain, Canary Islands') ||
              (isset($address['country_str']) && $address['country_str'] == 'Spain, Canary Islands'));

        if(!$address['is_europe']) $withVat = false;
        if($address['is_europe'] && !empty($address['business_number1'])) $withVat = false;
        if($address['code'] == 'AX') $withVat = false; // Аландские острова
        if($address['code'] == 'FI') $withVat = true; // В Финляндию всегда VAT
        if($isCanarie) $withVat = false;

        return $withVat;
    }

    public function tableName()
    {
        return 'user_address';
    }

    public function relations()
    {
        return array(
            'billingCountry' => array(self::BELONGS_TO, 'Country', array('country' => 'id')),
            'deliveryCountry' => array(self::BELONGS_TO, 'Country', array('country' => 'id')),
            'usstate'=> array(self::BELONGS_TO, 'USState', array('id' => 'state_id')),
        );
    }

    public function attributeLabels()
    {
        return array(
            'receiver_first_name' => Yii::app()->ui->item('regform_firstname'),
            'receiver_last_name' => Yii::app()->ui->item('regform_lastname'),
            'country' => Yii::app()->ui->item('address_country'),
            'state_id' => Yii::app()->ui->item('address_state'),
            'city' => Yii::app()->ui->item('address_city'),
            'postindex' => Yii::app()->ui->item('address_postindex'),
            'streetaddress' => Yii::app()->ui->item('address_streetaddress'),
            'contact_phone' => Yii::app()->ui->item("address_contact_phone"),
            'contact_email' => Yii::app()->ui->item("address_contact_email"),
        );
    }

    public function rules()
    {
        return array(
            array('receiver_title_name, receiver_first_name, receiver_last_name, receiver_middle_name, '
                  .'city, postindex, streetaddress', 'checkLatin'),

            array('type, receiver_first_name, receiver_last_name, country, city, streetaddress,'
                      . 'contact_phone', 'required', 'on' => 'new'),
            array('country', 'checkCountry', 'on' => 'new'),
            array('postindex', 'checkPostIndex', 'on' => 'new'),
            array('notes, state_id, receiver_middle_name, receiver_title_name, business_title, business_number1', 'safe', 'on' => 'new'),
            array('business_title', 'iforg', 'on' => 'new'),

            array('id, type, receiver_first_name, receiver_last_name, country, city, streetaddress,'
                  . 'contact_phone', 'required', 'on' => 'edit'),
            array('country', 'checkCountry', 'on' => 'edit'),
            array('postindex', 'checkPostIndex', 'on' => 'edit'),
            array('notes, state_id, receiver_middle_name, receiver_title_name, business_title, business_number1', 'safe', 'on' => 'edit'),
            array('business_title', 'iforg', 'on' => 'edit'),

        );
    }

    public function checkPostIndex($attr, $params)
    {
        $country = $this->country;
        if($country != 101) // Ireland
        {
            $value = $this->$attr;
            if(empty($value))
            {
                $labels = $this->attributeLabels();
                $msg = Yii::t('yii','{attribute} cannot be blank.', array('{attribute}' => $labels[$attr]));
                $this->addError($attr, $msg);
            }
        }
    }

    public function checkLatin($attribute, $params)
    {
        $value = trim($this->$attribute);
        if(empty($value)) return;

        // http://en.wikipedia.org/wiki/Cyrillic_script_in_Unicode
//Cyrillic: U+0400–U+04FF, 256 characters
//Cyrillic Supplement: U+0500–U+052F, 48 characters
//Cyrillic Extended-A: U+2DE0–U+2DFF, 32 characters
//Cyrillic Extended-B: U+A640–U+A69F, 96 characters
//Phonetic Extensions: U+1D2B, U+1D78, 2 Cyrillic characters
//        $patterns = array('|[\x{0400}-\x{04ff}]|ui',
//                          '|[\x{0500}-\x{052f}]|ui',
//                          '|[\x{a640}-\x{a69f}]|ui',
//                          '|[\x{1d2b}-\x{1d78}]|ui',
//                         );
//        foreach($patterns as $pattern)
//            if(preg_match($pattern, $value))
//            {
//                $this->addError($attribute, Yii::app()->ui->item('ONLY_LATIN'));
//                return;
//            }

        //http://en.wikipedia.org/wiki/List_of_Unicode_characters
        $pattern = '|[^\x{0020}-\x{007e}\x{00a1}-\x{024f}]|ui';
        if(preg_match($pattern, $value))
        {
            $this->addError($attribute, Yii::app()->ui->item('ONLY_LATIN'));
        }
    }

    public function IfOrg($attribute, $v)
    {
        $value = trim($this->$attribute);
        if($this->type == Address::ORGANIZATION && empty($value))
            $this->addError($attribute, 'Empty');
    }

    public function checkCountry($attr, $params)
    {
        $country = $this->country;
        if (empty($country))
        {
            $labels = $this->attributeLabels();
            $msg = Yii::t('yii','{attribute} cannot be blank.', array('{attribute}' => $labels[$attr]));
            $this->addError($attr, $msg);
        }
        if ($country == 225 && empty($this->state_id)) $this->addError('state_id', 'Select State');
    }

    public function InsertNew($uid, $isDefault)
    {
        $transaction = Yii::app()->db->beginTransaction();
        try
        {
            $this->insert();
            $id = $this->id;


            $this->addAddresses($uid, $id, $isDefault);
//            if ($isDefault)
//            {
//                $sql = 'UPDATE users_addresses SET if_default=0 WHERE uid=:uid';
//                Yii::app()->db->createCommand($sql)->execute(array(':uid' => $uid));
//            }
//            $sql = 'INSERT INTO users_addresses (uid, address_id, if_default) VALUES '
//                . '(:uid, :id, :def)';
//
//            Yii::app()->db->createCommand($sql)->execute(array(':uid' => $uid,
//                                                               ':id' => $id, ':def' => $isDefault ? 1 : 0));

            $transaction->commit();
            $this->RepairDefaultAddress($uid);
            return $id;
        }
        catch (Exception $ex)
        {
            $transaction->rollback();
            CommonHelper::LogException($ex, 'Failed to add user address');
            return false;
        }
    }

    public function IsMyAddress($uid, $addressID)
    {
        $sql = 'SELECT COUNT(*) FROM users_addresses WHERE uid=:uid AND address_id=:aid';
        $row = Yii::app()->db->createCommand($sql)->queryScalar(array(':uid' => $uid, ':aid' => $addressID));
        return $row;
    }

    public function GetAddress($uid, $addressID)
    {
        $sql = 'SELECT uas.*, ua.*, cl.title_en AS country_name, cl.*, IF(cl.id=68, 1, 0) AS is_finland, cl.code '
            . 'FROM users_addresses AS uas '
            . 'JOIN user_address AS ua ON uas.address_id=ua.id '
            . 'JOIN country_list AS cl ON ua.country=cl.id '
            . 'WHERE uas.uid=:uid AND uas.address_id=:aid';

        $row = Yii::app()->db->createCommand($sql)->queryRow(true, array(':uid' => $uid, ':aid' => $addressID));
        return $row;
    }

    public static function ReceiverName($address)
    {
        if (empty($address['receiver_name']))
        {
            $ret = array();
            if ($address['type'] == Address::ORGANIZATION)
            {
                $ret[] = $address['business_title'];
            }

            if (!empty($address['receiver_title_name'])) $ret[] = $address['receiver_title_name'];
            if (!empty($address['receiver_first_name'])) $ret[] = $address['receiver_first_name'];
            if (!empty($address['receiver_middle_name'])) $ret[] = $address['receiver_middle_name'];
            if (!empty($address['receiver_last_name'])) $ret[] = $address['receiver_last_name'];

            return implode(' ', $ret);
        }
        else
        {
            return $address['receiver_name'];
        }
    }

    public function GetAddresses($uid)
    {
        $sql = 'SELECT *, cl.title_en AS country_name, cl.code, ua.id as id FROM users_addresses AS uas '
              .'JOIN user_address AS ua ON uas.address_id=ua.id '
              .'LEFT JOIN country_list AS cl ON ua.country=cl.id '
              .'LEFT JOIN address_states_list AS asl ON ua.state_id=asl.id '
              .'WHERE uas.uid=:uid';

        $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':uid' => $uid));
        return $rows;
    }

    public function SetDefaultAddress($uid, $aid)
    {
        if(!$this->IsMyAddress($uid, $aid)) return false;

        $transaction = Yii::app()->db->beginTransaction();
        try
        {
            $sql = 'UPDATE users_addresses SET if_default=0 WHERE uid=:uid';
            Yii::app()->db->createCommand($sql)->execute(array(':uid' => $uid));
            $sql = 'UPDATE users_addresses SET if_default=1 WHERE uid=:uid AND address_id=:aid LIMIT 1';
            Yii::app()->db->createCommand($sql)->execute(array(':uid' => $uid, ':aid' => $aid));
            $transaction->commit();
            return true;
        }
        catch(Exception $ex)
        {
            CommonHelper::LogException($ex, 'Failed to set default address uid='.$uid.' aid='.$aid);
            $transaction->rollback();
            return false;
        }
    }

    public function DeleteAddress($uid, $aid)
    {
        $sql = 'DELETE FROM users_addresses WHERE uid=:uid AND address_id=:aid LIMIT 1';
        Yii::app()->db->createCommand($sql)->execute(array(':uid' => $uid, ':aid' => $aid));
        $this->RepairDefaultAddress($uid);
    }

    public function RepairDefaultAddress($uid)
    {
        $sql = 'SELECT COUNT(*) FROM users_addresses WHERE uid=:uid AND if_default=1';
        $cnt = Yii::app()->db->createCommand($sql)->queryScalar(array(':uid' => $uid));
        if($cnt == 1) return false; // Адрес по-умолчанию есть, ничего не делаем

        $addresses = $this->GetAddresses($uid);
        if(!empty($addresses))
        {
            $this->SetDefaultAddress($uid, $addresses[0]['address_id']);
            return true;
        }

        return false;
    }

    public static function GetDefaultAddress($uid)
    {
        $sql = 'SELECT ua.*, cl.*, cl.title_en AS country_name, cl.title_en AS country_str FROM users_addresses AS uas
                JOIN user_address AS ua ON ua.id=uas.address_id
                JOIN country_list AS cl ON ua.country=cl.id
                WHERE uid=:uid AND if_default=1';
        $address = Yii::app()->db->createCommand($sql)->queryRow(true, array(':uid' => $uid));
        if(!empty($address)) return $address;

        // if none, than first one
        $sql = 'SELECT ua.*, cl.*, cl.title_en AS country_name, cl.title_en AS country_str FROM users_addresses AS uas
                JOIN user_address AS ua ON ua.id=uas.address_id
                JOIN country_list AS cl ON ua.country=cl.id
                WHERE uid=:uid ORDER BY ua.id DESC LIMIT 1';
        $address = Yii::app()->db->createCommand($sql)->queryRow(true, array(':uid' => $uid));
        return $address;
    }

    public function NotifyIfAddressChanged($uid, $oldID, $newAddress)
    {
        $sql = 'SELECT COUNT(*) AS cnt
 FROM users_orders AS uo
 JOIN users_orders_items AS uoi ON uo.id=uoi.oid
 WHERE uo.uid=:uid AND uoi.entity=:entity';

        $cnt = Yii::app()->db->createCommand($sql)->queryScalar(array(':uid' => $uid, ':entity' => Entity::PERIODIC));
        if($cnt == 0) return;


        $allAddresses = $this->GetAddresses($uid);

        $oldAddress = Address::model()->findByPk($oldID);

        $old = $oldAddress->attributes;
        $oldCountry = Country::GetCountryById($oldAddress['country']);
        $old['country_name'] = $oldCountry['title_en'];
        $newCountry = Country::GetCountryById($newAddress['country']);
        $newAddress['country_name'] = $newCountry['title_en'];

        $message = new YiiMailMessage('Ruslania: Address changed');
        $message->view = 'address_changed';
        $message->setBody(array(
            'old' => $old,
            'new' => $newAddress,
            'uid' => $uid,
            'all' => $allAddresses,
        ), 'text/html');
        $message->addTo('periodicals@ruslania.com');
        $message->from = 'periodicals@ruslania.com';
        $ret = @Yii::app()->mail->send($message);
    }

    function addAddresses($uid, $id, $isDefault) {
        if ($isDefault)
        {
            $sql = 'UPDATE users_addresses SET if_default=0 WHERE uid=:uid';
            Yii::app()->db->createCommand($sql)->execute(array(':uid' => $uid));
        }

        $sql = 'INSERT INTO users_addresses (uid, address_id, if_default) VALUES '
            . '(:uid, :id, :def)';

        Yii::app()->db->createCommand($sql)->execute(array(':uid' => $uid,
            ':id' => $id, ':def' => $isDefault ? 1 : 0));
    }
}