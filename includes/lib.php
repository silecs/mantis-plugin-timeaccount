<?php
/* 
 * @license http://www.gnu.org/licenses/gpl-3.0.html  GNU GPL v3
 */

namespace timeaccount;

/**
 * Array of id, name, parent_id, timeused, tt.timecredit
 *
 * @return IteratorAggregate
 */
function readProjectsTime()
{
    $projectId = helper_get_current_project();
    if ($projectId == ALL_PROJECTS) {
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
 * Array of name, descriptin
 *
 * @return array
 */
function readNameDescription()
{
    $projectId = helper_get_current_project();
    if ($projectId == ALL_PROJECTS) {
        return "";
    }

    $timetable = plugin_table('project');
    $sql = "SELECT p.name, tt.description
        FROM {project} p
        LEFT JOIN $timetable tt ON tt.project_id = p.id
        WHERE p.id = " . (int) $projectId
        ;
    $result = db_query($sql);

    if (db_num_rows($result) == 0) {
        return [];
    }
    return $result->GetArray()[0];
}
