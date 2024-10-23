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
 * Strings for block block_graderanking.
 *
 * @package    block_graderanking
 * @copyright  2021 Antoni Oliver
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['title'] = 'Grade ranking';
$string['pluginname'] = 'Grade ranking';
$string['graderanking'] = 'Grade ranking';
$string['graderanking:addinstance'] = 'Add a new grade ranking block';
$string['graderanking:showgrade'] = 'Appear in grade rankings';
$string['blocktitle'] = 'Block title';
$string['category'] = 'Grade category';
$string['participant'] = 'Participant';
$string['decimals'] = 'Decimal digits';
$string['gradename'] = 'Grade name';
$string['grade'] = 'Grade';
$string['tableheight'] = 'Table height (px)';
$string['showusers'] = 'Show these users';
$string['alert'] = 'Alert';
$string['alert_content'] = "This block shows a ranking of a grade category.<br>Although the idea is to promote the students to complete their tasks, if these are part of the final grade of the subject, the effect may be the opposite and, furthermore, a list of (part of) the grades of the subject would be made public, which may not be desirable.<br>It is advisable to use this block for tasks that are optional, part of a gamification program or other things alike.<br>To make this work correctly, you will have to create a grade category in the gradebook, so it incldes the activities you want to consider.";
$string['alert_no_categories'] = "This course has no grade categories. This block cannot work until one has been configured.";
$string['root_category'] = 'Root category of the course';
$string['missing_category_id'] = 'The category id {$a} does not exist.';
$string['category_not_set_up'] = 'The category needs to be set up.';
$string['privacy:metadata'] = 'The grade ranking block only displays existing grade data.';
