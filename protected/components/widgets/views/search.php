<table cellspacing="0" cellpadding="0" border="0" class="divider myheader">
    <tbody>
    <tr>
        <td width="30" align="center"><img width="14" height="14" src="/pic1/arr2.gif"></td>
        <td class="leftmnutitle1-table-txt"><?=$ui->item("A_LEFT_SEARCH_WIN");?></td>
    </tr>
    </tbody>
</table>

<div style="background-color: white !important" class="divider">
    <form action="/search/" method=GET class="maintext">
        <?=$ui->item("A_LEFT_I_SEARCH");?>:<br>
        <input name="bsearch_expression" value="" type=text class=search1>
        <?=$ui->item("A_LEFT_WHERE_SEARCH");?>:<br>
        <select name="entity" class=search1>
            <option selected value="-1">везде
            <option value="1">Книги
            <option value="6">Ноты и книги о музыке
            <option value="4">Периодика
            <option value="2">Аудиокниги
            <option value="3">Видеопродукция
            <option value="7">Музыка
            <option value="8">Мультимедия
            <option value="9">Карты
            <option value="5">Печатная продукция и сувениры
        </select>
        <br/>
        <input type=image src="/pic1/<?=$ui->item("BTN_SEARCH_PICTURE");?>" alt="<?=$ui->item("BTN_SEARCH_ALT");?>">
    </form>

    <a href=""><?=$ui->item("A_ADVANCED_SEARCH");?></a>
</div>
