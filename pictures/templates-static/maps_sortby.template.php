<?

require_once("htmlcontrols.class.php");

class CTemplate_CatalogMapsSortby extends CCommonTask
{
    var $sortbyTitles = NULL;
    var $fields = NULL;

    function CTemplate_CatalogMapsSortby()
    {
        $this->sortbyTitles = new CCollection();
    }

    function setData(&$r)
    {
        if (!is_array($r) && sizeof($r) > 0)
        {
            trigger_error("CTemplate_CatalogMapsSortby::setData(...) receives incorrect parameter. ".
                          "Second parameter must be an one-member NAME-VALUE array.",
                          E_USER_ERROR);
        }
        else
        {
            $this->fields =& $r;
        }
    }

    function getText()
    {
        $ui =& $this->ui;
        $s  =& $this->sortbyTitles;

        $s->add(SORTBY_ALL_TITLE_ASC,       $ui->item("SORTBY_ALL_TITLE_ASC"));
        $s->add(SORTBY_ALL_TITLE_DESC,      $ui->item("SORTBY_ALL_TITLE_DESC"));
        $s->add(SORTBY_ALL_PRICE_ASC,       $ui->item("SORTBY_ALL_PRICE_ASC"));
        $s->add(SORTBY_ALL_PRICE_DESC,      $ui->item("SORTBY_ALL_PRICE_DESC"));
        $s->add(SORTBY_ALL_POPULAR_DESC,    $ui->item("SORTBY_ALL_POPULAR_DESC"));
        $s->add(SORTBY_ALL_POPULAR_ASC,     $ui->item("SORTBY_ALL_POPULAR_ASC"));
        $s->add(SORTBY_ALL_DATE_ADD_ASC,    $ui->item("SORTBY_ALL_ADD_DATE_ASC"));
        $s->add(SORTBY_ALL_DATE_ADD_DESC,   $ui->item("SORTBY_ALL_ADD_DATE_DESC"));

        $html = new HTMLControlsHelper();

        return $html->createSingle_SELECT("class=sort", $this->fields, $s);
    }
}

return new CTemplate_CatalogMapsSortby();

?>