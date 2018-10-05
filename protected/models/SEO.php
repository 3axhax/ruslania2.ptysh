<?php
/**
 * Created by Aleksandr Oleynik.
 * User: sankes
 * Date: 13.09.2018
 * Time: 12:35
 */

class SEO extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    function seo_change_meta_books_view($data, $entity) {

        switch (Yii::app()->language) {

            case 'ru' :
                $aut = '';
                $aut2 = '';
                if ($data['Authors'][0]) {
                    $aut = ProductHelper::GetTitle($data['Authors'][0]) . ' | ';
                    $aut2 = ProductHelper::GetTitle($data['Authors'][0]) . ', ';
                }
                $this->pageTitle = ProductHelper::GetTitle($data). ' | '.$aut.''.$data['isbn'].' | Купить книгу';

                $row = Binding::GetBinding($entity, $data['binding_id']);

                $this->pageDescription = ProductHelper::GetTitle($data). ', '.$aut2.''.$data['isbn'].', '.$row['title_' . Yii::app()->language].' - купить онлайн с доставкой по всему миру.';

                break;
            case 'en' :
                $aut = '';
                $aut2 = '';
                if ($data['Authors'][0]) {
                    $aut = ProductHelper::GetTitle($data['Authors'][0]) . ' | ';
                    $aut2 = ' by '.ProductHelper::GetTitle($data['Authors'][0]) . ', ';
                }
                $this->pageTitle = ProductHelper::GetTitle($data). ' | '.$aut.''.$data['isbn'].' | Buy book online';

                $row = Binding::GetBinding($entity, $data['binding_id']);

                $this->pageDescription = ProductHelper::GetTitle($data). ''.$aut2.''.$data['isbn'].', '.$row['title_' . Yii::app()->language].', Russian edition, buy online at Ruslania with worldwide delivery.';

                break;
            case 'fi' :
                $aut = '';
                $aut2 = '';
                if ($data['Authors'][0]) {
                    $aut = ProductHelper::GetTitle($data['Authors'][0]) . ' | ';
                    $aut2 = ' by '.ProductHelper::GetTitle($data['Authors'][0]) . ', ';
                }
                $this->pageTitle = ProductHelper::GetTitle($data). ' | '.$aut.''.$data['isbn'].' | osta kirja netistä Ruslania.comista.';

                $row = Binding::GetBinding($entity, $data['binding_id']);

                $this->pageDescription = ProductHelper::GetTitle($data). ''.$aut2.''.$data['isbn'].', '.$row['title_' . Yii::app()->language].', venäjänkielinen painos, osta netistä Ruslania.comista: nopea, kätevä ja edullinen toimitus - olemme Suomessa.';
                break;

            case 'se' :
                $aut = '';
                $aut2 = '';
                if ($data['Authors'][0]) {
                    $aut = ProductHelper::GetTitle($data['Authors'][0]) . ' | ';
                    $aut2 = ' by '.ProductHelper::GetTitle($data['Authors'][0]) . ', ';
                }
                $this->pageTitle = ProductHelper::GetTitle($data). ' | '.$aut.''.$data['isbn'].' | köp boken på nätet på Ruslania.com';

                $row = Binding::GetBinding($entity, $data['binding_id']);

                $this->pageDescription = ProductHelper::GetTitle($data). ''.$aut2.''.$data['isbn'].', '.$row['title_' . Yii::app()->language].',  Ryska upplagan, köp online på Ruslania.com med leverans till Sverige.';
                break;

            case 'de' :
                $aut = '';
                $aut2 = '';
                if ($data['Authors'][0]) {
                    $aut = ProductHelper::GetTitle($data['Authors'][0]) . ' | ';
                    $aut2 = ' by '.ProductHelper::GetTitle($data['Authors'][0]) . ', ';
                }
                $this->pageTitle = ProductHelper::GetTitle($data). ' | '.$aut.''.$data['isbn'].' | Kaufen Sie das Buch online';

                $row = Binding::GetBinding($entity, $data['binding_id']);

                $this->pageDescription = ProductHelper::GetTitle($data). ''.$aut2.''.$data['isbn'].', '.$row['title_' . Yii::app()->language].', Russische Ausgabe, online kaufen bei Ruslania.com mit weltweiter Lieferung.';
                break;

            case 'fr' :
                $aut = '';
                $aut2 = '';
                if ($data['Authors'][0]) {
                    $aut = ProductHelper::GetTitle($data['Authors'][0]) . ' | ';
                    $aut2 = ' by '.ProductHelper::GetTitle($data['Authors'][0]) . ', ';
                }
                $this->pageTitle = ProductHelper::GetTitle($data). ' | '.$aut.''.$data['isbn'].' | Acheter le livre en ligne';

                $row = Binding::GetBinding($entity, $data['binding_id']);

                $this->pageDescription = ProductHelper::GetTitle($data). ''.$aut2.''.$data['isbn'].', '.$row['title_' . Yii::app()->language].', Édition russe, acheter sur une magasin en ligne finlandais Ruslania.com avec la livraison dans le monde entier.';
                break;

            case 'es' :
                $aut = '';
                $aut2 = '';
                if ($data['Authors'][0]) {
                    $aut = ProductHelper::GetTitle($data['Authors'][0]) . ' | ';
                    $aut2 = ' by '.ProductHelper::GetTitle($data['Authors'][0]) . ', ';
                }
                $this->pageTitle = ProductHelper::GetTitle($data). ' | '.$aut.''.$data['isbn'].' | Compre el libro en línea';

                $row = Binding::GetBinding($entity, $data['binding_id']);

                $this->pageDescription = ProductHelper::GetTitle($data). ''.$aut2.''.$data['isbn'].', '.$row['title_' . Yii::app()->language].', Edición en ruso, compre en línea en la tienda web finlandesa Ruslania.com con entrega en todo el mundo.';
                break;

        }

    }

    function seo_change_meta_books_category($entity, $total, $catTitle, $cid, $catInfo = '') {

        switch (Yii::app()->language) {
            case 'ru' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = 'Интернет магазин русских книг Руслания в Финляндии с доставкой по всему миру';
                    $this->pageDescription = 'Интернет-магазин русских книг Руслания с доставкой по всему миру. В нашем каталоге более '.$total.' книг различных авторов и жанров.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' | Интернет-магазин русских книг Руслания';
                    $this->pageDescription = $catTitle . ' - купить русские книги из каталога интернет-магазина';
                }
                break;
            case 'en' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = 'Ruslania bookstore - buy Russian books online in '.geoip_country_name_by_name($_SERVER['REMOTE_ADDR']);
                    $this->pageDescription = 'Russian bookstore Ruslania - buy books online with worldwide delivery. More than '.$total.' books in our catalogue.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' books | Russian bookstore Ruslania';
                    $this->pageDescription = $catTitle . ' Russian books - more than '.$total.' books in the category at Ruslania.com.';
                }
                break;
            case 'fi' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = 'Ruslania.com-nettikirjakauppa Suomessa - Osta venäläisiä kirjoja netistä '.geoip_country_name_by_name($_SERVER['REMOTE_ADDR']);
                    $this->pageDescription = 'Osta kirjoja netistä suomalaisesta nettikaupasta Ruslania.com. Yli '.$total.' kirjaa valikoimissamme.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' - osta venäläisiä kirjoja netistä suomalaisesta Ruslania.com-nettikaupasta';
                    $this->pageDescription = $catTitle . ' venäläisiä kirjoja netistä – yli '.$total.' kirjaa kategoriassa  Ruslania.com-nettikaupasta. Olemme Suomessa.';
                }
                break;

            case 'se' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = 'Ruslania.com bokhandel i Finland - köp ryska böcker på nätet i '.geoip_country_name_by_name($_SERVER['REMOTE_ADDR']);
                    $this->pageDescription = 'Bokhandel Ruslania.com i Finland - Köp ryska böcker på nätet med världsomspännande leverans. Mer än '.$total.' böcker i vår katalog.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' - böcker | Ryska böcker från en finsk bokhandel Ruslania.com';
                    $this->pageDescription = $catTitle . ' Ryska böcker - mer än '.$total.' böcker i kategorin på Ruslania.com med världsomspännande leverans.';
                }
                break;

            case 'de' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = 'Ruslania.com Buchladen in Finnland - russische Bücher online kaufen in '.geoip_country_name_by_name($_SERVER['REMOTE_ADDR']);
                    $this->pageDescription = 'Buchhandlung Ruslania.com in Finnland - russische Bücher online kaufen mit weltweiter Lieferung. Mehr als '.$total.' Bücher in unserem Katalog.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' Bücher | Russische Bücher von einer finnischen Buchhandlung Ruslania.com';
                    $this->pageDescription = $catTitle . ' Russische Bücher - mehr als '.$total.' Bücher in der Kategorie bei Ruslania.com mit weltweiter Lieferung.';
                }
                break;

            case 'fr' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = 'Librairie Ruslania.com en Finlande - acheter des livres russes en ligne dans '.geoip_country_name_by_name($_SERVER['REMOTE_ADDR']);
                    $this->pageDescription = 'Ruslania.com en Finlande - acheter des livres russes en ligne avec une livraison dans le monde entier. Plus de '.$total.' livres dans notre catalogue.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' böcker | Ryska böcker från en finsk bokhandel Ruslania.com';
                    $this->pageDescription = $catTitle . ' Livres russes - plus de '.$total.' livres dans la catégorie à Ruslania.com avec la livraison mondiale.';
                }
                break;

            case 'es' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = 'Librería Ruslania.com en Finlandia - compre libros rusos en línea en '.geoip_country_name_by_name($_SERVER['REMOTE_ADDR']);
                    $this->pageDescription = 'Librería Ruslania.com en Finlandia: compre libros rusos en línea con entrega en todo el mundo. Más de '.$total.' libros en nuestro catálogo.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' libros | Libros rusos de una librería finlandesa Ruslania.com';
                    $this->pageDescription = $catTitle . ' Libros rusos: más de '.$total.' libros en la categoría en Ruslania.com con entrega en todo el mundo.';
                }
                break;

        }

    }


    function seo_change_meta_sheets_category($entity, $total, $catTitle, $cid, $catInfo = '') {

        switch (Yii::app()->language) {
            case 'ru' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = 'Ноты в интернет-магазине Руслания в Финляндии с доставкой по всему миру';
                    $this->pageDescription = 'Ноты для различных инструментов в интернет-магазине Руслания с доставкой по всему миру. В нашем каталоге более '.$total.' нотных изданий.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' - ноты в интернет-магазине Руслания';
                    $this->pageDescription = $catTitle . ' - купить ноты из каталога интернет-магазина Руслания.';
                }
                break;
            case 'en' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = 'Sheet music - buy printed sheet music online in '.geoip_country_name_by_name($_SERVER['REMOTE_ADDR']);
                    $this->pageDescription = 'Sheet music - buy online with worldwide delivery. More than '.$total.' editions in our catalogue.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' - sheet music | Ruslania store';
                    $this->pageDescription = $catTitle . ' sheet music - more than /number of items/ items in the category at Ruslania.com.';
                }
                break;
            case 'fi' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = 'Nuotti – osta nuotteja netissä '.geoip_country_name_by_name($_SERVER['REMOTE_ADDR']);
                    $this->pageDescription = 'Nuotteja – osta netistä kotiin toimitettuna. Yli '.$total.' tuotetta valikoimissamme.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' - nuotit | Ruslania-nettikauppa';
                    $this->pageDescription = $catTitle . 'nuotit – yli '.$total.' tuotetta tässä kategoriassa Ruslania.comissa';
                }
                break;

            case 'se' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = 'Noter – Köp noter på nätet till '.geoip_country_name_by_name($_SERVER['REMOTE_ADDR']);
                    $this->pageDescription = ' Noter – Köp på nätet med leverans över hela världen. Mer än '.$total.' noter i vår katalog.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' - noter | Ruslania-nätbutik';
                    $this->pageDescription = $catTitle . ' noter - mer än '.$total.' noter i kategorin på webbutiken Ruslania.com.';
                }
                break;

            case 'de' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = 'Noten - kaufen Sie Noten online im '.geoip_country_name_by_name($_SERVER['REMOTE_ADDR']);
                    $this->pageDescription = 'Noten - online kaufen mit weltweiter Lieferung. Mehr als '.$total.' Editionen in unserem Katalog.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' - Noter | Ruslania-Webshop';
                    $this->pageDescription = $catTitle . ' Noten - mehr als '.$total.' Artikel in der Kategorie auf Ruslania.com.';
                }
                break;

            case 'fr' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = 'Partitions - acheter des partitions en ligne en '.geoip_country_name_by_name($_SERVER['REMOTE_ADDR']);
                    $this->pageDescription = 'Partitions - acheter en ligne avec livraison dans le monde entier. Plus de '.$total.' éditions dans notre catalogue.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' - partitions | Ruslania - magasin en ligne';
                    $this->pageDescription = $catTitle . ' partitions - plus de '.$total.' objets dans la catégorie à Ruslania.com.';
                }
                break;

            case 'es' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = 'Partituras: compre partituras en línea en '.geoip_country_name_by_name($_SERVER['REMOTE_ADDR']);
                    $this->pageDescription = 'Partituras: compre en línea con entrega en todo el mundo. Más de '.$total.' ediciones en nuestro catálogo.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' Partituras | Ruslania – tienda web';
                    $this->pageDescription = $catTitle . ' partitura: más de '.$total.' artículos en la categoría en Ruslania.com.';
                }
                break;

        }

    }

    function seo_change_meta_sheets_view($data, $entity) {

        switch (Yii::app()->language) {

            case 'ru' :
                $aut2 = '';
                if ($data['Authors'][0]) {
                    $aut2 = ProductHelper::GetTitle($data['Authors'][0]) . ', ';
                }
                $this->pageTitle = ProductHelper::GetTitle($data). ' | '.$data['isbn'].' | Купить онлайн';

                $this->pageDescription = ProductHelper::GetTitle($data). ', '.$aut2.''.$data['isbn'].' - купить онлайн с доставкой по всему миру.';

                break;
            case 'en' :
                $aut2 = '';
                if ($data['Authors'][0]) {
                    $aut2 = ' by '.ProductHelper::GetTitle($data['Authors'][0]) . ', ';
                }
                $this->pageTitle = ProductHelper::GetTitle($data). ' | '.$data['isbn'].' | Buy online';


                $this->pageDescription = ProductHelper::GetTitle($data). ''.$aut2.''.$data['isbn'].' - buy sheet music online at Ruslania with worldwide delivery.';

                break;
            case 'fi' :
                $aut2 = '';
                if ($data['Authors'][0]) {
                    $aut2 = ' by '.ProductHelper::GetTitle($data['Authors'][0]) . ', ';
                }
                $this->pageTitle = ProductHelper::GetTitle($data). ' | '.$data['isbn'].' | osta netistä';

                $this->pageDescription = ProductHelper::GetTitle($data). ''.$aut2.''.$data['isbn'].' - osta nuotit Ruslanian nettikaupasta: nopea, kätevä ja edullinen toimitus - olemme Suomessa.';
                break;

            case 'se' :
                $aut2 = '';
                if ($data['Authors'][0]) {
                    $aut2 = ' by '.ProductHelper::GetTitle($data['Authors'][0]) . ', ';
                }
                $this->pageTitle = ProductHelper::GetTitle($data). ' | '.$data['isbn'].' | köp på nätet';

                $this->pageDescription = ProductHelper::GetTitle($data). ''.$aut2.''.$data['isbn'].' - köpa noter på nätet på Ruslania med leverans till Sverige.';
                break;

            case 'de' :
                $aut2 = '';
                if ($data['Authors'][0]) {
                    $aut2 = ' by '.ProductHelper::GetTitle($data['Authors'][0]) . ', ';
                }
                $this->pageTitle = ProductHelper::GetTitle($data). ' | '.$data['isbn'].' | Kaufe online';

                $this->pageDescription = ProductHelper::GetTitle($data). ''.$aut2.''.$data['isbn'].' - Kaufe Noten online bei Ruslania mit weltweiter Lieferung.';
                break;

            case 'fr' :
                $aut2 = '';
                if ($data['Authors'][0]) {
                    $aut2 = ' by '.ProductHelper::GetTitle($data['Authors'][0]) . ', ';
                }
                $this->pageTitle = ProductHelper::GetTitle($data). ' | '.$data['isbn'].' | acheter en ligne';


                $this->pageDescription = ProductHelper::GetTitle($data). ''.$aut2.''.$data['isbn'].' - acheter partitions en ligne chez Ruslania avec la livraison dans le monde entier.';
                break;

            case 'es' :
                $aut2 = '';
                if ($data['Authors'][0]) {
                    $aut2 = ' by '.ProductHelper::GetTitle($data['Authors'][0]) . ', ';
                }
                $this->pageTitle = ProductHelper::GetTitle($data). ' | '.$data['isbn'].' | compre en línea';


                $this->pageDescription = ProductHelper::GetTitle($data). ''.$aut2.''.$data['isbn'].' - compre partituras en línea en Ruslania con entrega en todo el mundo.';
                break;

        }

    }


    function seo_change_meta_periodic_category($entity, $total, $catTitle, $cid, $catInfo = '') {

        switch (Yii::app()->language) {
            case 'ru' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = 'Оформить подписку на газеты и журналы онлайн | Руслания';
                    $this->pageDescription = 'Подписка на русские газеты и журналы. Различные варианты подписки - на 3, 6 и 12 месяцев.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' - подписаться на издание прямо на сайте.';
                    $this->pageDescription = $catTitle . ' - подписка на русские газеты и журналы на сайте ';
                }
                break;
            case 'en' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = 'Russian magazines and newspapers subscription';
                    $this->pageDescription = 'Subscribe to Russian magazines and newspapers all around the world. Choose one of 3, 6 or 12 months subscriptions.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' - subscribe to Russian magazines and newspapers at Ruslania.com';
                    $this->pageDescription = $catTitle . ' - subscribe online to Russian magazines and newspapers - 3, 6, 12 months subscriptions.';
                }
                break;
            case 'fi' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = 'Tilaa venäläisiä sanoma- ja aikakauslehtiä';
                    $this->pageDescription = 'FI Tilaa venäläisiä sanoma- ja aikakauslehtiä Suomeen. Voit valita 3:n, 6:n ja 12:n kuukauden tilauksen.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' - tilaa venäläiset sanoma- ja aikakauslehdet Ruslaniasta';
                    $this->pageDescription = $catTitle . ' Tilaa venäläisiä sanoma- ja aikakauslehtiä Suomeen. Voit valita 3:n, 6:n ja 12:n kuukauden tilauksen.';
                }
                break;

            case 'se' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = 'Ryska tidningar och tidningar abonnemang';
                    $this->pageDescription = 'Prenumerera på ryska tidningar och tidningar runt om i världen. Välj en av 3, 6 eller 12 månaders prenumeration.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' Ryska tidningar och tidningar abonnemang på Ruslania.com';
                    $this->pageDescription = $catTitle . ' Prenumerera på ryska tidningar och tidningar runt om i världen. Välj en av 3, 6 eller 12 månaders prenumeration.';
                }
                break;

            case 'de' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = 'Russische Zeitschriften und Zeitungen abonnieren';
                    $this->pageDescription = 'Abonnieren Sie russische Zeitschriften und Zeitungen auf der ganzen Welt. Wählen Sie ein Abonnement von 3, 6 oder 12 Monaten.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' - Russische Zeitschriften und Zeitungen abonnieren auf Ruslania.com';
                    $this->pageDescription = $catTitle . ' Abonnieren Sie russische Zeitschriften und Zeitungen auf der ganzen Welt. Wählen Sie ein Abonnement von 3, 6 oder 12 Monaten.';
                }
                break;

            case 'fr' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = 'Abonnement aux magazines et journaux russes';
                    $this->pageDescription = 'Abonnez-vous aux magazines et journaux russes dans le monde entier. Choisissez l\'un des 3, 6 ou 12 mois d\'abonnement.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' - Abonnement aux magazines et journaux russes en Ruslania.com';
                    $this->pageDescription = $catTitle . ' Abonnez-vous aux magazines et journaux russes dans le monde entier. Choisissez l\'un des 3, 6 ou 12 mois d\'abonnement.';
                }
                break;

            case 'es' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = 'Revista rusa de revistas y periódicos';
                    $this->pageDescription = 'Suscríbete a revistas y periódicos rusos de todo el mundo. Elija una suscripción de 3, 6 o 12 meses.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' Revista rusa de revistas y periódicos en Ruslania.com';
                    $this->pageDescription = $catTitle . ' Suscríbete a revistas y periódicos rusos de todo el mundo. Elija una suscripción de 3, 6 o 12 meses.';
                }
                break;

        }

    }

    function seo_change_meta_periodic_view($data, $entity) {

        switch (Yii::app()->language) {

            case 'ru' :
                $binding = ProductHelper::GetTypes($entity, $data['type']);

                $this->pageTitle = 'Подписка на '.ProductHelper::GetTitle($binding).' '.ProductHelper::GetTitle($data). ' - русское издание';

                $subsisbn='';
                if ($data['isbn']) {
                    $subsisbn = ', '.$data['isbn'];
                }

                $this->pageDescription = ProductHelper::GetTitle($binding).' '.ProductHelper::GetTitle($data). $subsisbn . ' - подписаться онлайн.';

                break;
            case 'en' :
                $binding = ProductHelper::GetTypes($entity, $data['type']);

                $this->pageTitle = ProductHelper::GetTitle($binding).' '.ProductHelper::GetTitle($data). ' subscription - '.ProductHelper::GetTitle($data). ' Russian edition';

                $subsisbn='';
                if ($data['isbn']) {
                    $subsisbn = ' Subscribe to '.$data['isbn'].'.';
                }

                $this->pageDescription = 'Subscribe online to '.ProductHelper::GetTitle($data).' '.ProductHelper::GetTitle($binding).' Russian edition at Ruslania.com.'.$subsisbn;

                break;
            case 'fi' :
                $binding = ProductHelper::GetTypes($entity, $data['type']);

                $this->pageTitle = ProductHelper::GetTitle($binding).' '.ProductHelper::GetTitle($data). ' lehtitilaus - '.ProductHelper::GetTitle($data). ' venäläinen versio';

                $subsisbn='';
                if ($data['isbn']) {
                    $subsisbn = ' Tilaa '.$data['isbn'].'.';
                }

                $this->pageDescription = 'Tilaa lehtitilaus netistä '.ProductHelper::GetTitle($data).' '.ProductHelper::GetTitle($binding).' venäläinen versio netistä.'.$subsisbn;
                break;

            case 'se' :
                $binding = ProductHelper::GetTypes($entity, $data['type']);

                $this->pageTitle = ProductHelper::GetTitle($binding).' '.ProductHelper::GetTitle($data). ' prenumeration - '.ProductHelper::GetTitle($data). ' ryska version';

                $subsisbn='';
                if ($data['isbn']) {
                    $subsisbn = ' Prenumerera '.$data['isbn'].'.';
                }

                $this->pageDescription = 'Prenumerera '.ProductHelper::GetTitle($data).' '.ProductHelper::GetTitle($binding).' på ryska version på Ruslania.com.'.$subsisbn;
                break;

            case 'de' :
                $binding = ProductHelper::GetTypes($entity, $data['type']);

                $this->pageTitle = ProductHelper::GetTitle($binding).' '.ProductHelper::GetTitle($data). ' Abonnement - '.ProductHelper::GetTitle($data). ' Russische version';

                $subsisbn='';
                if ($data['isbn']) {
                    $subsisbn = ' Subscribe to '.$data['isbn'].'.';
                }

                $this->pageDescription = 'Abonnieren Sie online '.ProductHelper::GetTitle($data).' '.ProductHelper::GetTitle($binding).' Russische Ausgabe auf Ruslania.com.'.$subsisbn;
                break;

            case 'fr' :
                $binding = ProductHelper::GetTypes($entity, $data['type']);

                $this->pageTitle = ProductHelper::GetTitle($binding).' '.ProductHelper::GetTitle($data). ' Abonnement - '.ProductHelper::GetTitle($data). ' Édition russe';

                $subsisbn='';
                if ($data['isbn']) {
                    $subsisbn = ' Abonnez-vous á '.$data['isbn'].'.';
                }

                $this->pageDescription = 'Abonnez-vous en ligne á '.ProductHelper::GetTitle($data).' '.ProductHelper::GetTitle($binding).' l\'édition russe en Ruslania.com.'.$subsisbn;

                break;

            case 'es' :
                $binding = ProductHelper::GetTypes($entity, $data['type']);

                $this->pageTitle = ProductHelper::GetTitle($binding).' '.ProductHelper::GetTitle($data). ' suscripción - '.ProductHelper::GetTitle($data). ' Edición rusa';

                $subsisbn='';
                if ($data['isbn']) {
                    $subsisbn = ' Suscríbete a '.$data['isbn'].'.';
                }

                $this->pageDescription = 'Suscríbase en línea a '.ProductHelper::GetTitle($data).' '.ProductHelper::GetTitle($binding).' edición en ruso en Ruslania.com.'.$subsisbn;
                break;

        }

    }


    function seo_change_meta_music_category($entity, $total, $catTitle, $cid, $catInfo = '') {

        switch (Yii::app()->language) {
            case 'ru' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = 'Русская музыка на CD, DVD в интернет-магазине Руслания';
                    $this->pageDescription = 'Каталог русской музыки различных жанров и на различных носителях в интернет-магазине Руслания с доставкой по всему миру.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' - купить музыку на различных носителях';
                    $this->pageDescription = $catTitle . ' - купить музыку русских и зарубежных исполнителей различного формата в магазине Rusliania.com.';
                }
                break;
            case 'en' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = 'Russian music on CD, DVD | Ruslania.com';
                    $this->pageDescription = 'Buy Russian music on CD, DVD with worldwide delivery at Ruslania.com.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' - buy music online at Ruslania.com';
                    $this->pageDescription = $catTitle . ' - Russian and other music on CD, DVD - buy online at Ruslania.com.
';
                }
                break;
            case 'fi' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = 'Osta venäläistä musiikkia CD:nä ja DVD:nä netistä | Ruslania.comista';
                    $this->pageDescription = 'Osta venäläistä musiikkia netistä CD:nä ja DVD:nä suomalaisesta nettikaupasta Ruslania.com.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' - osta venäläistä musiikkia netistä CD:nä ja DVD:nä Ruslania.com-nettikaupasta';
                    $this->pageDescription = $catTitle . ' osta venäläistä musiikkia netistä CD:nä ja DVD:nä Ruslania.com-nettikaupasta. Olemme Suomessa.';
                }
                break;

            case 'se' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = 'Rysk musik online på CD och DVD  | webshop Ruslania.com';
                    $this->pageDescription = 'Köp rysk musik på CD och DVD online med leverans över hela världen på webshop Ruslania.com';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' - köp rysk musik i nätet på webshop Ruslania.com';
                    $this->pageDescription = $catTitle . ' Köp rysk musik på CD och DVD online med leverans över hela världen på webshop Ruslania.com';
                }
                break;

            case 'de' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = 'Russische musik - CD, DVD | Ruslania.com';
                    $this->pageDescription = 'Kaufen Sie russische Musik auf CD und DVD online mit weltweiter Lieferung auf Ruslania.com.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' - Kaufen Sie russische musik online auf Ruslania.com';
                    $this->pageDescription = $catTitle . ' Kaufen Sie russische Musik auf CD und DVD online mit weltweiter Lieferung auf Ruslania.com.';
                }
                break;

            case 'fr' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = 'Musique russe en CD, DVD |  en ligne en Ruslania.com';
                    $this->pageDescription = 'Achetez de musique russe en CD et DVD en ligne avec une livraison dans le monde entier sur Ruslania.com.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' - Achetez de musique russe sur magasin en ligne Ruslania.com';
                    $this->pageDescription = $catTitle . ' Achetez de musique russe en CD et DVD en ligne avec une livraison dans le monde entier sur Ruslania.com.';
                }
                break;

            case 'es' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = 'Compre la música rusa en línea en tienda web Ruslania.com';
                    $this->pageDescription = 'Compre música rusa en CD y DVD en linea con entrega en todo el mundo en tienda web Ruslania.com.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' - compre la música rusa en línea en tienda web Ruslania.com';
                    $this->pageDescription = $catTitle . ' Compre música rusa en CD y DVD en linea con entrega en todo el mundo en tienda web Ruslania.com.';
                }
                break;

        }

    }

    function seo_change_meta_music_view($data, $entity) {

        switch (Yii::app()->language) {

            case 'ru' :
                $aut = '';
                $aut2 = '';
                if ($data['Performers'][0]) {
                    $aut = ' - '.ProductHelper::GetTitle($data['Performers'][0]);
                    $aut2 = ProductHelper::GetTitle($data['Performers'][0]) . ', ';
                }

                $this->pageTitle = ProductHelper::GetTitle($data).''.$aut.' - '.$data['Media']['title'].' - купить онлайн';

                $this->pageDescription = ProductHelper::GetTitle($data).', '.$aut2.''.$data['eancode'].', '.$data['Media']['title'].' - купить онлайн с доставкой по всему миру.';

                break;
            case 'en' :
                $aut = '';
                $aut2 = '';
                if ($data['Performers'][0]) {
                    $aut = ' - '.ProductHelper::GetTitle($data['Performers'][0]);
                    $aut2 = ProductHelper::GetTitle($data['Performers'][0]) . ', ';
                }

                $this->pageTitle = ProductHelper::GetTitle($data).''.$aut.' - '.$data['Media']['title'].' - buy online';

                $this->pageDescription = ProductHelper::GetTitle($data).', '.$aut2.''.$data['eancode'].', '.$data['Media']['title'].' - buy online with worldwide delivery at Ruslania.com.';

                break;
            case 'fi' :
                $aut = '';
                $aut2 = '';
                if ($data['Performers'][0]) {
                    $aut = ' - '.ProductHelper::GetTitle($data['Performers'][0]);
                    $aut2 = ProductHelper::GetTitle($data['Performers'][0]) . ', ';
                }

                $this->pageTitle = ProductHelper::GetTitle($data).''.$aut.' - '.$data['Media']['title'].' - buy online osta netistä Ruslania.com.';

                $this->pageDescription = ProductHelper::GetTitle($data).', '.$aut2.''.$data['eancode'].', '.$data['Media']['title'].' - osta netistä Ruslania.comista: nopea, kätevä ja edullinen toimitus - olemme Suomessa.';
                break;

            case 'se' :
                $aut = '';
                $aut2 = '';
                if ($data['Performers'][0]) {
                    $aut = ' - '.ProductHelper::GetTitle($data['Performers'][0]);
                    $aut2 = ProductHelper::GetTitle($data['Performers'][0]) . ', ';
                }

                $this->pageTitle = ProductHelper::GetTitle($data).''.$aut.' - '.$data['Media']['title'].' - köp i nätet på Ruslania.com';

                $this->pageDescription = ProductHelper::GetTitle($data).', '.$aut2.''.$data['eancode'].', '.$data['Media']['title'].' - köpa rysk musik på nätet på Ruslania med leverans till Sverige.';
                break;

            case 'de' :
                $aut = '';
                $aut2 = '';
                if ($data['Performers'][0]) {
                    $aut = ' - '.ProductHelper::GetTitle($data['Performers'][0]);
                    $aut2 = ProductHelper::GetTitle($data['Performers'][0]) . ', ';
                }

                $this->pageTitle = ProductHelper::GetTitle($data).''.$aut.' - '.$data['Media']['title'].' - Kaufe online auf Ruslania.com';

                $this->pageDescription = ProductHelper::GetTitle($data).', '.$aut2.''.$data['eancode'].', '.$data['Media']['title'].' - Kaufe online bei Webshop Ruslania.com mit weltweiter Lieferung.';
                break;

            case 'fr' :
                $aut = '';
                $aut2 = '';
                if ($data['Performers'][0]) {
                    $aut = ' - '.ProductHelper::GetTitle($data['Performers'][0]);
                    $aut2 = ProductHelper::GetTitle($data['Performers'][0]) . ', ';
                }

                $this->pageTitle = ProductHelper::GetTitle($data).''.$aut.' - '.$data['Media']['title'].' - acheter en ligne en Ruslania.com';

                $this->pageDescription = ProductHelper::GetTitle($data).', '.$aut2.''.$data['eancode'].', '.$data['Media']['title'].' - acheter en ligne chez Ruslania.com avec la livraison dans le monde entier.';
                break;

            case 'es' :
                $aut = '';
                $aut2 = '';
                if ($data['Performers'][0]) {
                    $aut = ' - '.ProductHelper::GetTitle($data['Performers'][0]);
                    $aut2 = ProductHelper::GetTitle($data['Performers'][0]) . ', ';
                }

                $this->pageTitle = ProductHelper::GetTitle($data).''.$aut.' - '.$data['Media']['title'].' - compre en línea en tienda web Ruslania.com';

                $this->pageDescription = ProductHelper::GetTitle($data).', '.$aut2.''.$data['eancode'].', '.$data['Media']['title'].' - compre en línea en Ruslania con entrega en todo el mundo.';
                break;

        }

    }


    function seo_change_meta_other_category($entity, $total, $catTitle, $cid, $catInfo = '') {

        switch (Yii::app()->language) {
            case 'ru' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = Entity::GetTitle(Entity::ConvertToSite($entity)) . ' - купить '.mb_strtolower(Entity::GetTitle(Entity::ConvertToSite($entity))).' в интернет-магазине Руслания';
                    $this->pageDescription = Entity::GetTitle(Entity::ConvertToSite($entity)) . ' - каталог русской продукции в интернет-магазине Руслания с доставкой по всему миру.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' - купить '.mb_strtolower($catTitle).' в интернет-магазине Руслания';
                    $this->pageDescription = $catTitle . ' - '.$total.' товаров в русском магазине Rusliania.com.';
                }
                break;
            case 'en' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = Entity::GetTitle(Entity::ConvertToSite($entity)) . ' - buy Russian '.mb_strtolower(Entity::GetTitle(Entity::ConvertToSite($entity))).' online at Ruslania.com';
                    $this->pageDescription = 'Buy russian ' . Entity::GetTitle(Entity::ConvertToSite($entity)) . ' with worldwide delivery at Ruslania.com.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' - buy russian '.mb_strtolower($catTitle).' online at Ruslania.com';
                    $this->pageDescription = $catTitle . ' - more than '.$total.' available online at Ruslania.com with worldwide delivery.';
                }
                break;
            case 'fi' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = Entity::GetTitle(Entity::ConvertToSite($entity)) . ' - osta '.mb_strtolower(Entity::GetTitle(Entity::ConvertToSite($entity))).' netistä Ruslania.comista';
                    $this->pageDescription = 'osta venäläiset ' . Entity::GetTitle(Entity::ConvertToSite($entity)) . ' suomalaisesta nettikaupasta Ruslania.com: toimitus joka puolelle maailmaa!';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' - osta '.mb_strtolower($catTitle).' netistä Ruslania.com-nettikaupasta';
                    $this->pageDescription = $catTitle . ' - yli '.$total.' tuotetta tässä kategoriassa Ruslania.com-nettikaupassa. Olemme Suomessa.';
                }
                break;

            case 'se' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = Entity::GetTitle(Entity::ConvertToSite($entity)) . ' - köp ryska '.mb_strtolower(Entity::GetTitle(Entity::ConvertToSite($entity))).' online på webshop Ruslania.com';
                    $this->pageDescription = 'Köp ryska ' . Entity::GetTitle(Entity::ConvertToSite($entity)) . ' online med leverans över hela världen på webshop Ruslania.com';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' - köp ryska '.mb_strtolower($catTitle).' i nätet på webshop Ruslania.com';
                    $this->pageDescription = $catTitle . ' - mer än '.$total.' i kategorin med leverans över hela världen på webshop Ruslania.com.';
                }

            case 'de' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = Entity::GetTitle(Entity::ConvertToSite($entity)) . ' - Kaufen Sie russische '.mb_strtolower(Entity::GetTitle(Entity::ConvertToSite($entity))).' online auf Ruslania.com';
                    $this->pageDescription = 'Kaufen Sie russische ' . Entity::GetTitle(Entity::ConvertToSite($entity)) . ' online mit weltweiter Lieferung auf Ruslania.com.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' - Kaufen Sie russische '.mb_strtolower($catTitle).' online auf Ruslania.com';
                    $this->pageDescription = $catTitle . ' - mehr als '.$total.' Artikel in der Kategorie mit weltweiter Lieferung auf Ruslania.com.';
                }
                break;

            case 'fr' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = Entity::GetTitle(Entity::ConvertToSite($entity)) . ' - Achetez de '.mb_strtolower(Entity::GetTitle(Entity::ConvertToSite($entity))).' russe avec une livraison dans le monde entier sur Ruslania.com.';
                    $this->pageDescription = 'Achetez de ' . Entity::GetTitle(Entity::ConvertToSite($entity)) . ' russe avec une livraison dans le monde entier sur Ruslania.com.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' - Achetez de '.mb_strtolower($catTitle).' russe sur magasin en ligne Ruslania.com';
                    $this->pageDescription = $catTitle . ' - plus de '.$total.' objets dans la catégorie russe avec une livraison dans le monde entier sur Ruslania.com.';
                }
                break;

            case 'es' :
                if ($cid == 0) { //если начальный раздел
                    $this->pageTitle = Entity::GetTitle(Entity::ConvertToSite($entity)) . ' - compre  '.mb_strtolower(Entity::GetTitle(Entity::ConvertToSite($entity))).' rusa en línea en Ruslania.com';
                    $this->pageDescription = 'Compre ' . Entity::GetTitle(Entity::ConvertToSite($entity)) . ' rusa con entrega en todo el mundo en Ruslania.com.';
                    $this->pageKeywords = '';
                } else {
                    $this->pageTitle = $catTitle . ' - compre '.mb_strtolower($catTitle).' rusa en línea en tienda web Ruslania.com';
                    $this->pageDescription = $catTitle . ' - más de '.$total.' disponible en línea en tienda web Ruslania.com con entrega en todo el mundo.';
                }
                break;

        }

    }

    function seo_change_meta_other_view($data, $entity) {

        switch (Yii::app()->language) {

            case 'ru' :
                $eanisbn = '';
                if ($data['eancode']) {

                    $eanisbn = ', '. $data['eancode'];

                } elseif ($data['isbn']) {

                    $eanisbn = ', '. $data['isbn'];

                }

                $this->pageTitle = ProductHelper::GetTitle($data) . $eanisbn .' - купить онлайн в интернет-магазине Ruslania.com';

                $this->pageDescription = ProductHelper::GetTitle($data).' - купить онлайн с доставкой по всему миру в интернет-магазине Руслания.';

                break;
            case 'en' :
                $eanisbn = '';
                if ($data['eancode']) {

                    $eanisbn = ', '. $data['eancode'];

                } elseif ($data['isbn']) {

                    $eanisbn = ', '. $data['isbn'];

                }

                $this->pageTitle = ProductHelper::GetTitle($data) . $eanisbn .' - buy online at Ruslania.com';

                $this->pageDescription = ProductHelper::GetTitle($data).' - buy online with worldwide delivery at Ruslania.com.';

                break;
            case 'fi' :
                $eanisbn = '';
                if ($data['eancode']) {

                    $eanisbn = ', '. $data['eancode'];

                } elseif ($data['isbn']) {

                    $eanisbn = ', '. $data['isbn'];

                }

                $this->pageTitle = ProductHelper::GetTitle($data) . $eanisbn .' - osta netistä Ruslania.com.';

                $this->pageDescription = ProductHelper::GetTitle($data).' - osta netistä Ruslania.comista: nopea, kätevä ja edullinen toimitus - olemme Suomessa.';
                break;

            case 'se' :
                $eanisbn = '';
                if ($data['eancode']) {

                    $eanisbn = ', '. $data['eancode'];

                } elseif ($data['isbn']) {

                    $eanisbn = ', '. $data['isbn'];

                }

                $this->pageTitle = ProductHelper::GetTitle($data) . $eanisbn .' - köp i nätet på Ruslania.com';

                $this->pageDescription = ProductHelper::GetTitle($data).' - köpa noter på nätet på Ruslania med leverans till Sverige.';
                break;

            case 'de' :
                $eanisbn = '';
                if ($data['eancode']) {

                    $eanisbn = ', '. $data['eancode'];

                } elseif ($data['isbn']) {

                    $eanisbn = ', '. $data['isbn'];

                }

                $this->pageTitle = ProductHelper::GetTitle($data) . $eanisbn .' - Kaufe online auf Ruslania.com';

                $this->pageDescription = ProductHelper::GetTitle($data).' - Kaufe online bei Webshop Ruslania.com mit weltweiter Lieferung.';
                break;

            case 'fr' :
                $eanisbn = '';
                if ($data['eancode']) {

                    $eanisbn = ', '. $data['eancode'];

                } elseif ($data['isbn']) {

                    $eanisbn = ', '. $data['isbn'];

                }

                $this->pageTitle = ProductHelper::GetTitle($data) . $eanisbn .' - acheter en ligne en Ruslania.com';

                $this->pageDescription = ProductHelper::GetTitle($data).' - acheter en ligne chez Ruslania.com avec la livraison dans le monde entier.';
                break;

            case 'es' :
                $eanisbn = '';
                if ($data['eancode']) {

                    $eanisbn = ', '. $data['eancode'];

                } elseif ($data['isbn']) {

                    $eanisbn = ', '. $data['isbn'];

                }

                $this->pageTitle = ProductHelper::GetTitle($data) . $eanisbn .' - compre en línea en tienda web Ruslania.com';

                $this->pageDescription = ProductHelper::GetTitle($data).' - compre en línea en Ruslania con entrega en todo el mundo.';
                break;

        }

    }


}