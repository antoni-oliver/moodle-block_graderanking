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
    public function init() {
        $this->title = get_string('graderanking', 'block_graderanking');
    }

    public function get_content() {
        global $PAGE, $CFG, $COURSE, $USER;

        require_once($CFG->libdir . '/grade/grade_category.php');
        require_once($CFG->libdir . '/grade/grade_item.php');
        require_once($CFG->libdir . '/grade/grade_grade.php');

        # To add the javascript to center the ranking on the student. 
        $PAGE->requires->js_call_amd('block_graderanking/graderanking', 'init');

        if ($this->content !== null) {
            return $this->content;
        } else {
            $this->content = new stdClass;

            # if the category has not been set up, we cannot continue
            if (!$this->config || !$this->config->categoryid) {
                $this->content->text = get_string('category_not_set_up', 'block_graderanking');
                return;
            }

            # enrolled users
            $users = get_enrolled_users($this->context, 'mod/assignment:submit');

            # selected category
            $cat = grade_category::fetch(array('courseid' => $COURSE->id, 'id' => $this->config->categoryid));

            # if we cannot find the category (e.g. it was removed), we cannot continue
            if (!$cat) {
                $this->content->text = get_string('missing_category_id', 'block_graderanking', $this->config->categoryid);
                return;
            }
            
            # we obtain the grade item for the category
            $cat_item = $cat->get_grade_item();

            # we obtain all the grades on that grade item
            $grades = grade_grade::fetch_all(array('courseid' => $COURSE->id, 'itemid' => $cat_item->id));

            # we will store the users' grades here
            $usergrades = array();

            # get grades as userid -> [grade => finalgrade]
            if ($grades) {
                foreach ($grades as $grade) {
                    # we check that the user is enrolled (so as not to get teachers)
                    if (array_key_exists($grade->userid, $users)) {
                        $usergrades[$grade->userid] = array('grade' => $grade->finalgrade);
                    }
                }
            }

            # we add user info userid -> [firstname => firstname, lastname => lastname, grade => grade]
            if ($users) {
            foreach ($users as $userid => $user) {
                    $usergrade = $usergrades[$userid];
                    $usergrade['userid']    = $userid;
                    $usergrade['firstname']    = $user->firstname;
                    $usergrade['lastname']    = $user->lastname;
                    $usergrade['grade']     = number_format($usergrade['grade'], $this->config->decimals);
                    $usergrades[$userid] = $usergrade;
                }
            }

            # we sort the entries
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

            # we build the ranking positions
            for ($i = 0, $last_grade = NULL, $n = 1; $i < count($usergrades); $i++) {
                $usergrade = $usergrades[$i];
                if ($last_grade !== $usergrade['grade']) {
                    $n = $i + 1;    
                }
                $usergrade['n'] = $n;
                $last_grade = $usergrade['grade'];
                $usergrades[$i] = $usergrade;
            }

            # we build the final table
            $tablebody = "";
            foreach ($usergrades as $grade) {
                $me = $grade['userid'] == $USER->id ? " class=\"me\"" : "";
                $n = $grade['n'];
                $fn = $grade['firstname'];
                $ln = $grade['lastname'];
                $g = $grade['grade'];
                $tablebody .= "<tr$me><td>$n</td><td><div class=\"studentname\">$fn $ln</div></td><td>$g</td></tr>";
            }

            # header for the table
            $huser = get_string('participant', 'block_graderanking');
            $hgrade = $this->config->gradename;
            $tablehead = "<tr><th>#</th><th>$huser</th><th>$hgrade</th></tr>";

            # table max height
            $style = "";
            if ($this->config->tableheight) {
                $style .= "max-height: {$this->config->tableheight}px;\n";
                $style .= "overflow-y: scroll;\n";
            }

            # final content
            $table = "<div class=\"graderanking\" style=\"$style\"><table class=\"generaltable table-sm\" width=\"100%\"><thead>$tablehead</thead><tbody>$tablebody</tbody></table></div>";
            $this->content->text = $table;
        }
        return $this->content;
    }

    public function specialization() {
        if (isset($this->config)) {
            if (empty($this->config->title)) {
                $this->title = get_string('defaulttitle', 'block_graderanking');
            } else {
                $this->title = $this->config->title;
            }
        }
    }

    public function instance_allow_multiple() {
          return true;
    }
}
