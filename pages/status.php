<?php
/*
 * @license http://www.gnu.org/licenses/gpl-3.0.html  GNU GPL v3
 */

require_once dirname(__DIR__) . '/includes/lib.php';

access_ensure_project_level(config_get('view_summary_threshold'));

$projectId = helper_get_current_project();
$timerows = \timeaccount\readProjectsTime($projectId);
$info = \timeaccount\readNameDescription($projectId);
$flashMessages = \timeaccount\readSessionMessages();

html_page_top();

if ($flashMessages) {
    echo '<div class="flash-messages well">';
    foreach ($flashMessages as $category => $messages) {
        foreach ($messages as $m) {
            echo '<div class="alert alert-' . $category . '">' . $m . "</div>\n";
        }
    }
    echo '</div>';
}
?>
<h1>
    <?= isset($info['name']) ? htmlspecialchars($info['name']) . ' — ' : '' ?>
    Décompte du temps
</h1>

<div id="time-per-project" class="summary-container">
    <h2>Crédits de temps</h2>
    <table>
        <thead>
            <tr class="row-category">
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
    <p>
        Les durées sont exprimées sous la forme <em>hh:mm</em> (heures et minutes).
    </p>
</div>

<?php
if (isset($info['description'])) {
    echo "<h2>Détails</h2>";
    echo '<div id="project-info">'
    . nl2br($info['description']) // no filter, but on purpose!
    . "</div>\n";
}
?>

<?php
if (isset($info['id']) && timeaccount\canCreditTime($info['id'])) {
    $credit = ($info['timecredit'] ? db_minutes_to_hhmm($info['timecredit']) : '');
    ?>
    <h2>Administation du crédit de temps</h2>
    <div class="form-container">
        <form method="post" action="<?= plugin_page('update-project') ?>">
            <fieldset>
                <legend><?= htmlspecialchars($info['name']) ?></legend>

                <input type="hidden" name="project_id" value="<?= $info['id'] ?>" />

                <div class="field-container">
                    <label><span>Crédit de temps</span></label>
                    <span class="input">
                        <input type="text" name="timecredit" value="<?= $credit ?>" />
                        (hh:mm ou hh.h)
                    </span>
                    <span class="label-style"></span>
                </div>

                <div class="field-container">
                    <label><span>Commentaire public</span><br />(HTML brut + nl2br)</label>
                    <span class="input">
                        <textarea cols="74" rows="12" name="description"><?= htmlspecialchars($info['description']) ?></textarea>
                    </span>
                    <span class="label-style"></span>
                </div>

                <span class="submit-button">
                    <button type="submit" class="button">Enregistrer</button>
                </span>
            </fieldset>
        </form>
    </div>
    <?php
}
?>

<?php
html_page_bottom();
