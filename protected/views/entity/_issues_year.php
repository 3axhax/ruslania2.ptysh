<?php if (empty($item['issues_year'])) return; ?>

<?php

list($month, $label_for_month, $issues, $label_for_issues, $x_issues_in_year, $issues_year) = PereodicsTypes::model()->issuesYear($item['issues_year']);

echo '<br/>';
echo sprintf($x_issues_in_year, $issues_year) . ", ";

$msg    = sprintf
(
    $ui->item("MIN_FOR_X_MONTHS_Y_ISSUES_TEMPLATE"),
    $month, $label_for_month,
    $issues, $label_for_issues
);

echo $msg;