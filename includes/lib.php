<?php
/* 
 * @license http://www.gnu.org/licenses/gpl-3.0.html  GNU GPL v3
 */

namespace timeaccount;

/**
 * Array of id, name, parent_id, timeused, tt.timecredit
 *
 * @param int $projectId
 * @return IteratorAggregate
 */
function readProjectsTime($projectId)
{
    if (empty($projectId) || $projectId == ALL_PROJECTS) {
        $projects = current_user_get_accessible_projects();
    } else {
        $projects = [(int) $projectId];
    }
    $ids = join(',', $projects);

    $timetable = plugin_table('project');
    $sql = "SELECT p.id, p.name, ph.parent_id, SUM(bn.time_tracking) AS timeused, tt.timecredit
            FROM {project} p
            LEFT JOIN {project_hierarchy} ph ON p.id = ph.child_id
            LEFT JOIN {bug} b ON b.project_id = p.id
            LEFT JOIN {bugnote} bn ON bn.bug_id = b.id
            LEFT JOIN $timetable tt ON tt.project_id = p.id
            WHERE p.id IN ($ids)
            GROUP BY p.id
            ORDER BY p.name";

    return db_query($sql);
}

/**
 * Array of id, name, timecredit, description
 *
 * @param int $projectId
 * @return array
 */
function readNameDescription($projectId)
{
    if (empty($projectId) || $projectId == ALL_PROJECTS) {
        return null;
    }

    $timetable = plugin_table('project');
    $sql = "SELECT p.id, p.name, tt.timecredit, tt.description
        FROM {project} p
        LEFT JOIN $timetable tt ON tt.project_id = p.id
        WHERE p.id = " . (int) $projectId
        ;
    $result = db_query($sql);

    if (db_num_rows($result) == 0) {
        return [];
    }
    $rows = $result->GetArray();
    return $rows[0];
}

/**
 * If the parameter contains no ':', then it implies a suffix ':00'.
 *
 * @param string $hhmm
 * @return integer
 */
function convertHhmmToMinutes($hhmm)
{
    $m = [];
    if (ctype_digit($hhmm)) {
        return 60 * ((int) $hhmm);
    } else if (preg_match('/^(\d+):(\d\d?)$/', $hhmm, $m)) {
        return $m[1] * 60 + $m[2];
    } else if (preg_match('/^(\d+\.\d\d?)$/', $hhmm, $m)) {
        return (int) (((float) $m[1]) * 60);
    } else {
        // invalid format
        return 0;
    }
}

/**
 * @param integer $projectId
 * @return boolean
 */
function canCreditTime($projectId)
{
    return access_get_project_level((int) $projectId) >= config_get('manage_project_threshold');
}

/**
 * @return boolean
 */
function addSessionMessage($category, $message)
{
    if (!isset($_SESSION['timeaccount_messages'])) {
        $_SESSION['timeaccount_messages'] = [];
    }
    if (!isset($_SESSION['timeaccount_messages'][$category])) {
        $_SESSION['timeaccount_messages'][$category] = [];
    }
    $_SESSION['timeaccount_messages'][$category][] = $message;
    return true;
}

/**
 * @return array
 */
function readSessionMessages()
{
    if (isset($_SESSION['timeaccount_messages'])) {
        $messages = $_SESSION['timeaccount_messages'];
        unset($_SESSION['timeaccount_messages']);
        return $messages;
    }
    return [];
}
