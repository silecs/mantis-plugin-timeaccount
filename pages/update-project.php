<?php
/*
 * @license http://www.gnu.org/licenses/gpl-3.0.html  GNU GPL v3
 */

require_once dirname(__DIR__) . '/includes/lib.php';

access_ensure_project_level(config_get('manage_project_threshold'));

if (isset($_POST['timecredit'])) {
    $id = (int) $_POST['project_id'];
    $timecredit = timeaccount\convertHhmmToMinutes((string) $_POST['timecredit']);
    $description = (string) $_POST['description'];

    if (timeaccount\canCreditTime($id)) {
        $info = \timeaccount\readNameDescription($id);
        $timetable = plugin_table('project');
        if ($info['timecredit'] === null) {
            $sql = sprintf("INSERT INTO %s VALUES (%d, %d, %s)", $timetable, $id, $timecredit, db_param());
        } else {
            $sql = sprintf(
                "UPDATE %s SET timecredit = %d, description = %s WHERE project_id = %d",
                $timetable, $timecredit, db_param(), $id
            );
        }
        db_query($sql, [$description]);
        \timeaccount\addSessionMessage('success', "La modification du crédit de temps a été enregistrée.");
    } else {
        \timeaccount\addSessionMessage('warning', "Permission refusée.");
    }
}

header("Location: " . plugin_page('status'));
exit();
