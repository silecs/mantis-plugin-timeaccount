<?php
/*
 * @license http://www.gnu.org/licenses/gpl-3.0.html  GNU GPL v3
 */

require_once dirname(__DIR__) . '/includes/lib.php';

access_ensure_project_level(config_get('time_tracking_view_threshold'));

$projectId = helper_get_current_project();
$userLevel = user_get_access_level(auth_get_current_user_id(), $projectId);

$timerows = \timeaccount\readProjectsTime($projectId);
$info = \timeaccount\readNameDescription($projectId);
$flashMessages = \timeaccount\readSessionMessages();

layout_page_header("Décompte du temps");
layout_page_begin();

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

<div>
    Voir aussi :
    <ul>
        <li>
            Le décompte par catégorie ou par développeur est sur une <a href="/plugin.php?page=TimeReporting/report">page de résumé</a>,
            avec la possibilité de filtrer par période.
        </li>
        <?php if (access_has_project_level(config_get('time_tracking_reporting_threshold'))) { ?>
        <li>
            Le temps consommé par chaque ticket peut être listé sur la page de <a href="/billing_page.php">suivi du temps par tickets</a>.
            La page liste les tickets actifs sur une période, avec leur décompte en temps.
        </li>
        <?php } ?>
    </ul>
</div>

<div class="col-md-12 col-xs-12">

<div id="time-per-project" class="widget-box widget-color-blue2">
    <div class="widget-header widget-header-small">
        <h2 class="widget-title lighter">
            <?php print_icon( 'fa-clock-o', 'ace-icon' ); ?>
            Crédits de temps
        </h2>
    </div>
    <div class="widget-body widget-main">
        <table class="table table-bordered table-condensed table-hover table-striped" style="max-width: 100ex">
            <thead>
                <tr class="row-category">
                    <th>Projet</th>
                    <th class="text-right">Temps consacré</th>
                    <th class="text-right">Temps prépayé</th>
                    <th class="text-right">Reste</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($timerows as $row) {
                    if ((int) $row['timecredit'] === 0 && (int) $row['timeused'] === 0) {
                        continue;
                    }
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo '<td class="text-right">' . db_minutes_to_hhmm($row['timeused']) . "</td>";
                    echo '<td class="text-right">' . ($row['timecredit'] > 0 ? db_minutes_to_hhmm($row['timecredit']) : "") . "</td>";
                    if ($row['timecredit'] > 0) {
                        $remains = $row['timecredit'] - $row['timeused'];
                        if ($remains > 0) {
                            $remains = db_minutes_to_hhmm($remains);
                        } else {
                            $remains = "<strong>− " . db_minutes_to_hhmm(-1*$remains) . "</strong>";
                        }
                    } else {
                        $remains = '';
                    }
                    echo '<td class="text-right">' . $remains . "</td>";
                    echo "</tr>\n";
                }
                ?>
            </tbody>
        </table>
        <p>
            Les durées sont exprimées sous la forme <em>hh:mm</em> (heures et minutes).
        </p>

        <?php
        if (isset($info['description'])) {
            echo "<hr /><h3>Détails pour ce projet</h3>";
            echo '<div id="project-info">'
            . nl2br($info['description']) // no filter, but on purpose!
            . "</div>\n";
        }
        ?>
    </div>
</div>

<?php if ($projectId > 0 && access_compare_level($userLevel, config_get('time_tracking_edit_threshold'))) { ?>
<div class="widget-box widget-color-blue2">
    <div class="widget-header widget-header-small">
        <h2 class="widget-title lighter">
            <?php print_icon( 'fa-clock-o', 'ace-icon' ); ?>
            Administration du crédit de temps
        </h2>
    </div>
    <div class="widget-body widget-main">
        <h3><?= htmlspecialchars($info['name']) ?></h3>
        <?php
        if (isset($info['id']) && timeaccount\canCreditTime($info['id'])) {
            $credit = ($info['timecredit'] ? db_minutes_to_hhmm($info['timecredit']) : '');
            ?>
            <div class="form-container">
                <form method="post" action="<?= plugin_page('update-project') ?>" class="form-horizontal">
                    <input type="hidden" name="project_id" value="<?= $info['id'] ?>" />
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Crédit de temps</label>
                        <div class="col-sm-10">
                            <input type="text" name="timecredit" value="<?= $credit ?>" />
                            (hh:mm ou hh.h)
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Commentaire public<br />(HTML brut + retours lignes conservés)</label>
                        <div class="col-sm-10">
                            <textarea cols="74" rows="12" name="description"><?= htmlspecialchars($info['description']) ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-default">Enregistrer</button>
                        </div>
                    </div>
                </form>
            </div>
            <?php
        }
        ?>
    </div>
</div>
<?php } ?>

</div>

<?php
layout_page_end();
