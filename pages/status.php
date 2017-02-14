<?php
/* 
 * @license http://www.gnu.org/licenses/gpl-3.0.html  GNU GPL v3
 */

require_once dirname(__DIR__) . '/includes/lib.php';

access_ensure_project_level(config_get('view_summary_threshold'));

$timerows = \timeaccount\readProjectsTime();
$descr = \timeaccount\readNameDescription();

html_page_top();
?>
<h1><?= isset($descr['name']) ? $descr['name'] . ' — ' : '' ?>Décompte du temps</h1>

<?php
if (isset($descr['description'])) {
    echo nl2br(htmlspecialchars($descr['description']));
}
?>

<table>
   <thead>
       <tr>
           <th>Projet</th>
           <th>Temps consacré</th>
           <th>Temps prépayé</th>
           <th>Reste</th>
       </tr>
   </thead>
   <tbody>
        <?php
        foreach ($timerows as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . db_minutes_to_hhmm($row['timeused']) . "</td>";
            echo "<td>" . db_minutes_to_hhmm($row['timecredit']) . "</td>";
            $remains = $row['timecredit'] - $row['timeused'];
            if ($remains < 0) {
                $remains = 0;
            }
            echo "<td>" . db_minutes_to_hhmm($remains) . "</td>";
            echo "</tr>\n";
        }
        ?>
   </tbody>
</table>

<?php
html_page_bottom();
