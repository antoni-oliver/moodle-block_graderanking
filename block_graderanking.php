<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Displays a ranking of the participants' grades on a specific grade category.
 *
 * @package    block_graderanking
 * @copyright  2021 Antoni Oliver
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_graderanking extends block_base {
    /**
     * Initialises the block.
     */
    public function init() {
        $this->title = get_string('graderanking', 'block_graderanking');
    }

    /**
     * Builds the block's HTML content.
     */
    public function get_content() {
        global $CFG, $COURSE, $USER;

        require_once($CFG->libdir . '/grade/constants.php');
        require_once($CFG->libdir . '/grade/grade_category.php');
        require_once($CFG->libdir . '/grade/grade_item.php');
        require_once($CFG->libdir . '/grade/grade_grade.php');

        // To add the javascript to center the ranking on the student.
        $this->page->requires->js_call_amd('block_graderanking/graderanking', 'init');

        if ($this->content !== null) {
            return $this->content;
        } else {
            $this->content = new stdClass;

            // If the category has not been set up, we cannot continue.
            if (!$this->config || !$this->config->categoryid) {
                $this->content->text = get_string('category_not_set_up', 'block_graderanking');
                return;
            }

            // We get the enrolled users.
            $users = get_enrolled_users($this->context, 'moodle/grade:view');

            // We fetch the category.
            $cat = grade_category::fetch(['courseid' => $COURSE->id, 'id' => $this->config->categoryid]);

            // If we cannot find the category (e.g. it was removed), we cannot continue.
            if (!$cat) {
                $this->content->text = get_string('missing_category_id', 'block_graderanking', $this->config->categoryid);
                return;
            }

            // We obtain the grade item for the category.
            $catitem = $cat->get_grade_item();

            // We obtain all the grades on that grade item.
            $grades = grade_grade::fetch_all(['courseid' => $COURSE->id, 'itemid' => $catitem->id]);

            // We will store the users' grades here.
            $usergrades = [];

            // We get grades as userid -> [grade => finalgrade].
            if ($grades) {
                foreach ($grades as $grade) {
                    // We check that the user is enrolled (so as not to get teachers).
                    if (array_key_exists($grade->userid, $users)) {
                        $usergrades[$grade->userid] = ['grade' => $grade->finalgrade];
                    }
                }
            }

            // We add user info as userid -> [firstname => firstname, lastname => lastname, grade => grade].
            if ($users) {
                foreach ($users as $userid => $user) {
                    $usergrade = $usergrades[$userid];
                    $usergrade['userid'] = $userid;
                    $usergrade['fullname'] = fullname($user);
                    $usergrade['grade'] = number_format($usergrade['grade'], $this->config->decimals);
                    $usergrades[$userid] = $usergrade;
                }
            }

            // We sort the entries.
            usort($usergrades, function ($a, $b) {
                if ($a['grade'] == $b['grade']) {
                    if ($a['userid'] == $b['userid']) {
                        return 0;
                    } else if ($a['userid'] < $b['userid']) {
                        return -1;
                    } else {
                        return +1;
                    }
                } else if ($a['grade'] < $b['grade']) {
                    return +1;
                } else {
                    return -1;
                }
            });

            // We build the ranking positions.
            for ($i = 0, $lastgrade = null, $n = 1; $i < count($usergrades); $i++) {
                $usergrade = $usergrades[$i];
                if ($lastgrade !== $usergrade['grade']) {
                    $n = $i + 1;
                }
                $usergrade['n'] = $n;
                $lastgrade = $usergrade['grade'];
                $usergrades[$i] = $usergrade;
            }

            // We build the final table.
            $tablebody = "";
            foreach ($usergrades as $grade) {
                $me = $grade['userid'] == $USER->id ? " class=\"me\"" : "";
                $n = $grade['n'];
                $fullname = $grade['fullname'];
                $g = $grade['grade'];
                $tablebody .= "<tr$me><td>$n</td><td><div class=\"studentname\">$fullname</div></td><td>$g</td></tr>";
            }

            // Header for the table.
            $huser = get_string('participant', 'block_graderanking');
            $hgrade = $this->config->gradename;
            $tablehead = "<tr><th>#</th><th>$huser</th><th>$hgrade</th></tr>";

            // Set table max height if applicable.
            $style = "";
            if ($this->config->tableheight) {
                $style .= "max-height: {$this->config->tableheight}px;\n";
                $style .= "overflow-y: scroll;\n";
            }

            // Final content.
            $table = "<div class=\"graderanking_container\" style=\"$style\">" .
            "<table class=\"generaltable table-sm\" width=\"100%\"><thead>$tablehead</thead><tbody>$tablebody</tbody></table>" .
            "</div>";
            $this->content->text = $table;
        }
        return $this->content;
    }

    /**
     * Configures the block title.
     */
    public function specialization() {
        if (isset($this->config)) {
            if (empty($this->config->title)) {
                $this->title = get_string('defaulttitle', 'block_graderanking');
            } else {
                $this->title = $this->config->title;
            }
        }
    }

    /**
     * Allow multiple instances.
     */
    public function instance_allow_multiple() {
          return true;
    }

    /**
     * Do not allow the block to be added to the My Moodle page.
     */
    public function applicable_formats() {
        return ['all' => true, 'my' => false, 'tag' => false];
    }
}
