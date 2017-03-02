<?php
/**
 * @license http://www.gnu.org/licenses/gpl-3.0.html  GNU GPL v3
 */

/**
 * TimeAccount is a Mantis plugin.
 *
 * @author François Gannaz <francois.gannaz@silecs.info>
 */
class TimeAccountPlugin extends MantisPlugin
{
    /**
     * Init the plugin attributes.
     */
    function register()
    {
        $this->name = 'Time Account';
        $this->description = "Plugin that reports the allocated time and the used time.";
        $this->page = 'status';

        $this->version = '1.0';
        $this->requires = [
            'MantisCore' => '1.3.0, < 2.0',
        ];

        $this->author = 'François Gannaz / Silecs';
        $this->contact = 'francois.gannaz@silecs.info';
        $this->url = '';
    }

    /**
     * @return array
     */
    public function schema()
    {
        return [
            // first command
            [
                'CreateTableSQL', // function name
                [// function parameters
                    plugin_table('project'),
                    "project_id INT UNSIGNED NOT NULL PRIMARY, "
                    . "timecredit INT UNSIGNED NOT NULL DEFAULT 0, "
                    . "description TEXT NOT NULL",
                ],
            ],
        ];
    }

    /**
     * Declare hooks on Mantis events.
     *
     * @return array
     */
    public function hooks()
    {
        return [
            'EVENT_MENU_SUMMARY' => 'onMenuSummary',
        ];
    }

    /**
     * Add entries to the menu on the page "Summary".
     *
     * @return array
     */
    public function onMenuSummary()
    {
        return [
            '<a href="' . plugin_page('status') . '">Temps</a>',
        ];
    }
}
