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

        $this->version = '2.0';
        $this->requires = [
            'MantisCore' => '2.0',
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
            'EVENT_MENU_MAIN_FILTER' => 'onMenuMain',
        ];
    }

    /**
     * Add entries to the menu on the page "Billing".
     *
     * @return array
     */
    public function onMenuMain($eventName, $data)
    {
        //var_dump($data);
        if ($eventName !== 'EVENT_MENU_MAIN_FILTER') {
            return $data;
        }
        foreach ($data as $i => $d) {
            if ($d['url'] === 'billing_page.php') {
                $data[$i]['url'] = 'plugin.php?page=TimeAccount/status';
            }
        }
        return [$data];
    }
}
