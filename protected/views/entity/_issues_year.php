<?php if (empty($item['issues_year'])) return; ?>

<?php

$x_issues_in_year = NULL;
$issues_year = $item['issues_year']['issues_year'];

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

echo '<br/>';
echo sprintf($x_issues_in_year, $issues_year) . ", ";

$ret = array();
if ($issues_year >= 12)
{
    $ret[] = 3;
    $ret[] = 6;
    $ret[] = 12;
    //for($i = 6; $i <= 12; $i+=6) $ret[] = $i;
}
else
{
    $j = ceil(12 / $issues_year);
    if ($j < 3)
    {
        $j = 3;
    }
    for($i = $j; $i <= 12; $i += $j) $ret[] = $i;
}

$month  = $ret[0];

$issues =
    ($item['issues_year']['issues_year'] < 12) ?
        $month / round(12 / $item['issues_year']['issues_year']) :
        round($item['issues_year']['issues_year'] / 12) * $month;

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

echo $msg;