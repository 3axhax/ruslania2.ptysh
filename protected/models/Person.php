<?php
class Person
{
    const ROLE_AUTHOR = 1;
    const ROLE_PERFORMER = 2;
    const ROLE_ACTOR = 3;
    const ROLE_DIRECTOR = 4;
    const ROLE_PRODUCER = 5;

    public static function ConvertToCommonAuthor($authorID)
    {
        return 100000000+$authorID;
    }

    public static function ConvertToRealID($id)
    {
        return $id - 100000000;
    }
	
	public static function CreateUrl($item, $lang = null)
    {
        if ($item === false) return '';
        
        if (!empty($lang)&&($lang !== Yii::app()->language)&&!defined('OLD_PAGES')) $params['__langForUrl'] = $lang;
        return Yii::app()->createUrl($item, $params);
    }
	
	
}

/*

aentity:
1 - author all
2 - audio performer
3 - music performer
4 - video actor list
5 - video director list
6 - video producer list

1 - author, 2 - performer, 3 - actor, 4 - director, 5 - producer

TRUNCATE TABLE all_persons;
INSERT INTO all_persons (id, real_id, aentity, entity, title_ru, title_en, title_rut, title_fi, description_file_ru, description_file_en, description_file_fi, description_file_rut)
SELECT 100000000+id AS id, id AS real_id, 1 AS aentity, 10 AS entity, title_ru, title_en, title_rut, title_fi, description_file_ru, description_file_en, description_file_fi, description_file_rut FROM all_authorslist
UNION
SELECT 200000000+id AS id, id AS real_id, 2 AS aentity, 20 AS entity, title_ru, title_en, NULL AS title_rut, NULL AS title_fi, description_file_ru, description_file_en, description_file_fi, description_file_rut FROM audio_performerslist
UNION
SELECT 300000000+id AS id, id AS real_id, 3 AS aentity, 22 AS entity, title_ru, title_en, NULL AS title_rut, NULL AS title_fi, description_file_ru, description_file_en, description_file_fi, description_file_rut FROM music_performerslist
UNION
SELECT 400000000+id AS id, id AS real_id, 4 AS aentity, 40 AS entity, title_ru, title_en, NULL AS title_rut, NULL AS title_fi, NULL AS description_file_ru, NULL AS description_file_en, NULL AS description_file_fi, NULL AS description_file_rut FROM video_actorslist
UNION
SELECT 500000000+id AS id, id AS real_id, 5 AS aentity, 40 AS entity, title_ru, title_en, NULL AS title_rut, NULL AS title_fi, NULL AS description_file_ru, NULL AS description_file_en, NULL AS description_file_fi, NULL AS description_file_rut FROM video_directorslist
UNION
SELECT 600000000+id AS id, id AS real_id, 6 AS aentity, 40 AS entity, title_ru, title_en, NULL AS title_rut, NULL AS title_fi, NULL AS description_file_ru, NULL AS description_file_en, NULL AS description_file_fi, NULL AS description_file_rut FROM video_producerslist


// тут role_id из этого списка
// 1 - author, 2 - performer, 3 - actor, 4 - director, 5 - producer

TRUNCATE TABLE all_roles;
INSERT INTO all_roles (item_id, entity, role_id, person_id)
SELECT 200000000+audio_id AS item_id, 20 AS entity, 1 AS role_id, p.id AS person_id
FROM audio_authors AS a
JOIN all_persons AS p ON p.aentity=1 AND p.real_id=a.author_id
UNION

SELECT 200000000+audio_id AS item_id, 20 AS entity, 2 AS role_id, p.id AS person_id
FROM audio_performers AS a
JOIN all_persons AS p ON p.aentity=2 AND p.real_id=a.performer_id
UNION
SELECT 150000000+musicsheet_id AS item_id, 15 AS entity, 1 AS role_id, p.id AS person_id
FROM musicsheets_authors AS a
JOIN all_persons AS p ON p.aentity=1 AND p.real_id=a.author_id
UNION

SELECT 220000000+music_id AS item_id, 22 AS entity, 1 AS role_id, p.id AS person_id
FROM music_authors AS a
JOIN all_persons AS p ON p.aentity=1 AND p.real_id=a.author_id
UNION
SELECT 220000000+music_id AS item_id, 22 AS entity, 2 AS role_id, p.id AS person_id
FROM music_performers AS a
JOIN all_persons AS p ON p.aentity=3 AND p.real_id=a.performer_id
UNION

SELECT 500000000+printed_id AS item_id, 50 AS entity, 1 AS role_id, p.id AS person_id
FROM printed_authors AS a
JOIN all_persons AS p ON p.aentity=1 AND p.real_id=a.author_id
UNION

SELECT 240000000+soft_id AS item_id, 24 AS entity, 1 AS role_id, p.id AS person_id
FROM soft_authors AS a
JOIN all_persons AS p ON p.aentity=1 AND p.real_id=a.author_id
UNION

SELECT 400000000+video_id AS item_id, 40 AS entity, 3 AS role_id, p.id AS person_id
FROM video_actors AS a
JOIN all_persons AS p ON p.aentity=4 AND p.real_id=a.actor_id
UNION
SELECT 400000000+video_id AS item_id, 40 AS entity, 4 AS role_id, p.id AS person_id
FROM video_directors AS a
JOIN all_persons AS p ON p.aentity=5 AND p.real_id=a.director_id
UNION
SELECT 400000000+video_id AS item_id, 40 AS entity, 5 AS role_id, p.id AS person_id
FROM video_producers AS a
JOIN all_persons AS p ON p.aentity=6 AND p.real_id=a.producer_id
UNION

SELECT 100000000+book_id AS item_id, 10 AS entity, 1 AS role_id, p.id AS person_id
FROM books_authors AS a
JOIN all_persons AS p ON p.aentity=1 AND p.real_id=a.author_id








*/