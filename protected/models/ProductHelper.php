<?php

class ProductHelper
{
    
    public static function CutTitle($string, $length){
        $string = mb_substr($string, 0, $length,'UTF-8'); // обрезаем и работаем со всеми кодировками и указываем исходную кодировку
        $position = mb_strrpos($string, ' ', 'UTF-8'); // определение позиции последнего пробела. Именно по нему и разделяем слова
        $string = mb_substr($string, 0, $position, 'UTF-8'); // Обрезаем переменную по позиции
        
        if (mb_strlen($string) > $length) {
            $string = $string . '...';
        }
        
        return $string;
    }
    
    
    public static function GetTitle($item, $sKey = 'title', $cnt = 0)
    {
        if (empty($item)) return Yii::app()->ui->item('NO_DATA');
        $langs = array($sKey . '_se' => $sKey . '_en',
                       $sKey . '_fr' => $sKey . '_en',
                       $sKey . '_de' => $sKey . '_en',
                       $sKey . '_fi' => $sKey . '_en',
                       $sKey . '_en' => $sKey . '_rut',
                       $sKey . '_rut' => $sKey . '_ru',
                       $sKey . '_es' => $sKey . '_en',
        );

        $key = $sKey . '_' . Yii::app()->language;
        $ret = '';
        while (true)
        {
            //if (isset($item[$key]) && !empty($item[$key])) return trim($item[$key]);
            if(array_key_exists($key, $item))
            {
                $ret = trim($item[$key]);
                break;
            }
            $key = $langs[$key];
        }

        if(!empty($ret)) {
			
			
			$tmp = $ret;
			if ($cnt > 0) {
			$len = mb_strlen(trim($item[$key]), 'utf-8');
			$pos = false;
            
            if($len > $cnt) {
				if($len > $cnt) $tmp = mb_substr($ret, 0, $cnt, 'utf-8');
				if($len > 0) $tmp .= '...';
			}	
				
			}
			
            return $tmp;
			
		} 

        // Найти первую непустую колонку
        foreach($langs as $lang=>$data)
        {
            if(array_key_exists($lang, $item))
            {
                $val = trim($item[$lang]);
                if(!empty($val)) return $val;
            }
        }

        if(Yii::app()->language == 'ru' && isset($item[$sKey.'_ru']) && !empty($item[$sKey.'_ru']))
        {
			
			$ret = mb_strlen(trim($item[$sKey.'_ru']), 'utf-8');
			$tmp = $ret;
			if ($cnt > 0) {
			$len = mb_strlen(trim($item[$sKey.'_ru']), 'utf-8');
			$pos = false;
            if($len > $cnt) {
				if($len > $cnt) $tmp = mb_substr($ret, 0, $cnt, 'utf-8');
				if($len > 0) $tmp .= '...';
			}	
			}
			
            return $tmp;
        }
        else
        {
            $keys = array($sKey.'_ru', $sKey.'_rut');
            foreach($keys as $key)
            {
                if(array_key_exists($key, $item) && !empty($item[$key])) {
					
					$ret = mb_strlen(trim($item[$sKey.'_ru']), 'utf-8');
					$tmp = $ret;
					if ($cnt > 0) {
						$len = mb_strlen(trim($item[$sKey.'_ru']), 'utf-8');
						$pos = false;
						
						if($len > $cnt) $tmp = mb_substr($ret, 0, $cnt, 'utf-8');
						if($len > 0) $tmp .= '...';
						
					}
					return $tmp;	
				}				
            }
        }
        return '';
    }

    public static function FormatCurrency()
    {
        return Currency::ToSign(Yii::app()->currency);
    }

    public static function FormatPrice($price, $includeCurrency = true, $currency = null)
    {
        if (empty($price))
        {
            return '<b>EMPTY PRICE '.$price.'</b>';
        }

        $ret = number_format($price, 2, '.', ' ');
        if ($includeCurrency)
        {
            if (empty($currency)) $currency = Yii::app()->currency;
            $ret .= ' ' . Currency::ToSign($currency);
        }
        return $ret;
    }

    public static function GetDescription($item, $cnt = 0, $url = '')
    {
        $langs = array('description_se' => 'description_en',
                       'description_fi' => 'description_en',
                       'description_de' => 'description_en',
                       'description_en' => 'description_rut',
                       'description_rut' => 'description_ru',
                       'description_es' => 'description_en',
                       'description_fr' => 'description_en',
                       'description_ru' => 'description_ru');

        $key = 'description_' . Yii::app()->language;
        $ret = '';
        while (true)
        {
            if (isset($item[$key]) && !empty($item[$key]))
            {
                $ret = $item[$key];
                break;
            }
            if ($key == 'description_ru')
            {
                $ret = $item[$key];
                break;
            }
            $key = $langs[$key];
        }

        if(empty($ret))
        {
            foreach($langs as $key=>$field)
            {
                if(!empty($item[$key]))
                {
                    $ret = $item[$key];
                    break;
                }
            }
        }

        $tmp = $ret;
        if ($cnt > 0)
        {
            $pos = false;
            $len = strlen($tmp);
            if($len > $cnt) $pos = strpos($tmp, ' ', $cnt);
            if($pos !== false)  $tmp = substr($ret, 0, $pos);
            else $tmp = substr($ret, 0, $cnt);
            if($len > 0) { 
				
				if (!$url) {
				
					$tmp .= '...';
				
				} else {
					
					$tmp = strip_tags($tmp);
					
					$tmp .= '<a href="'.$url.'">...</a>';
					
				}
			}
        }

        //$tmp = preg_replace('/(\r\n)+/u', "\n", $tmp);
        return $tmp;
    }

    public static function CreateUrl($item)
    {
        if ($item === false) return '';
        
		
		
		if (is_numeric($item['entity'])) $item['entity'] = Entity::GetUrlKey($item['entity']);
		
		//var_dump($item);
		
        $title = self::ToAscii(self::GetTitle($item));
        return Yii::app()->createUrl('product/view', array('entity' => $item['entity'], 'id' => $item['id'],
                                                           'title' => $title));
    }

    public static function ToAscii($str, $options = array())
    {
        // Make sure string is in UTF-8 and strip invalid UTF-8 characters
        $str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());

        $defaults = array(
            'delimiter' => '-',
            'limit' => null,
            'lowercase' => true,
            'replacements' => array(),
            'transliterate' => true,
        );

        // Merge options
        $options = array_merge($defaults, $options);

        $char_map = array(
            // Latin
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O',
            'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
            'ß' => 'ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o',
            'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
            'ÿ' => 'y',

            // Latin symbols
            '©' => '(c)',

            // Greek
            'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
            'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
            'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
            'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
            'Ϋ' => 'Y',
            'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
            'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
            'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
            'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
            'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',

            // Turkish
            'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
            'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g',

            // Russian
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Jo', 'Ж' => 'Zh',
            'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
            'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'Kh', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Shh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'Eh', 'Ю' => 'Yu',
            'Я' => 'Ya',
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'jo', 'ж' => 'zh',
            'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
            'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'kh', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'eh', 'ю' => 'yu',
            'я' => 'ya',

            // Ukrainian
            'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
            'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',

            // Czech
            'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U',
            'Ž' => 'Z',
            'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
            'ž' => 'z',

            // Polish
            'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z',
            'Ż' => 'Z',
            'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
            'ż' => 'z',

            // Latvian
            'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N',
            'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
            'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
            'š' => 's', 'ū' => 'u', 'ž' => 'z'
        );

        // Make custom replacements
        $str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);

        // Transliterate characters to ASCII
        if ($options['transliterate'])
        {
            $str = str_replace(array_keys($char_map), $char_map, $str);
        }

        // Replace non-alphanumeric characters with our delimiter
        $str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);

        // Remove duplicate delimiters
        $str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);

        // Truncate slug to max. characters
        $str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');

        // Remove delimiter from ends
        $str = trim($str, $options['delimiter']);

        return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
    }

    public static function Link2Picture($data)
    {
        $pic = array_key_exists('image', $data) ? $data['image'] : 'no-image.jpg';
        return Yii::app()->params['PicDomain'].'/pictures/small/' . $pic;
    }

    public static function IsShelfId($q)
    {
        if (!is_numeric($q)) return false;
        $len = mb_strlen($q, 'utf-8');
        if ($len < 6 || $len > 8) return false;
        $q .= '';
        $firstSigns = mb_substr($q, 0, 2, 'utf-8');

        $shelfs = array();
        foreach (Entity::GetEntitiesList() as $entity=>$set) {
            if ($entity != 10) $shelfs[] = $entity;
        }

        if ($len == 6 && in_array($firstSigns, $shelfs)) return true;
        if ($len == 8 && $firstSigns == '10') return true;
        return false;
    }

    public static function IsEan($q)
    {
        if (is_numeric($q) && strlen($q . '') == 13) return true;
        return false;
    }

    public static function IsIsbn($isbn)
    {
        $isbn = str_replace('-', '', $isbn);
        $isDigit = ctype_digit($isbn);
        if(!$isDigit) return false;

        if (strlen($isbn) == 10)
        {
            $subTotal = 0;
            $mpBase = 10;
            for ($x = 0; $x <= 8; $x++)
            {
                $mp = $mpBase - $x;
                $subTotal += ($mp * $isbn{$x});
            }
            $rest = $subTotal % 11;
            $checkDigit = $isbn{9};
            if (strtolower($checkDigit) == "x" || $checkDigit == 0)
                $checkDigit = 10;
            return $checkDigit == (11 - $rest);
        }
        // going to perform isbn-13 check
        elseif (strlen($isbn) == 13)
        {
            $subTotal = 0;
            for ($x = 0; $x <= 11; $x++)
            {
                $mp = ($x + 1) % 2 == 0 ? 3 : 1;
                $subTotal += $mp * $isbn{$x};
            }
            $rest = $subTotal % 10;
            $checkDigit = $isbn{12};
            if (strtolower($checkDigit) == "x" || $checkDigit == 0)
                $checkDigit = 10;
            return $checkDigit == (10 - $rest);
        }
        else
        {
            return False;
        }
    }

    public static function IsAvailableForOrder($item)
    {
        $code = Availability::GetStatus($item);

        return $code == Availability::AVAIL_IN_SHOP ||
               $code == Availability::ENDING_IN_SHOP ||
               $code == Availability::TO_ORDER_SLOW ||
               $code == Availability::TO_ORDER_FAST;
    }
	
	public function GetBindingListForSelect($e) {
		
		$entities = Entity::GetEntitiesList();
        $eTable = $entities[$e]['binding_table'];
		
		$rows = array();
		
		if ($eTable) {
		
			$sql = 'SELECT * FROM `'.$eTable.'` WHERE title_ru LIKE "%обложка%" OR title_ru LIKE "%переплет%"';
			
			$res = Yii::app()->db->createCommand($sql)->queryAll();
			
			foreach ($res as $row) {
				$rows[] = array('ID'=>$row['id'], 'Name'=>$row['title_'.Yii::app()->language]);
			}
			
		}
		
		return $rows;
		
	}
	
	public function GetTypes($entity, $bid)
    {
        
        $table = '`pereodics_types`';

        $sql = 'SELECT * FROM '.$table.' WHERE id=:id';
        $row = Yii::app()->db->createCommand($sql)->queryRow(true, array(':id' => $bid));
        return $row;
    }
	
	public function GetTypesPrinted($entity, $bid)
    {
        
        $table = '`printed_types`';

        $sql = 'SELECT * FROM '.$table.' WHERE id=:id';
        $row = Yii::app()->db->createCommand($sql)->queryRow(true, array(':id' => $bid));
        return $row;
    }
	
	
	
	
}

