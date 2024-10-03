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
 * Edit form for block block_graderanking.
 *
 * @package    block_graderanking
 * @copyright  2021 Antoni Oliver
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_graderanking_edit_form extends block_edit_form {
    /**
     * Define the form.
     * @param moodleform $mform The form object.
     */
    protected function specific_definition($mform) {
        global $CFG, $COURSE;

        require_once($CFG->libdir . '/grade/constants.php');
        require_once($CFG->libdir . '/grade/grade_category.php');

        // Top warning.
        $mform->addElement(
            'static',
            'description',
            get_string('alert', 'block_graderanking'),
            get_string('alert_content', 'block_graderanking')
        );

        // Section header title according to language file.
        $mform->addElement(
            'header',
            'config_header',
            get_string('blocksettings', 'block')
        );

        // Title.
        $mform->addElement(
            'text',
            'config_title',
            get_string('blocktitle', 'block_graderanking')
        );
        $mform->setDefault('config_title', get_string('title', 'block_graderanking'));
        $mform->setType('config_title', PARAM_TEXT);

        // Category.
        $cats = grade_category::fetch_all(['courseid' => $COURSE->id]);
        if ($cats) {
            $items = [];
            foreach ($cats as $id => $cat) {
                if ($cat->parent == null) {
                    $items[$id] = get_string('root_category', 'block_graderanking');
                } else {
                    $items[$id] = $cat->fullname;
                }
            }
            $mform->addElement(
                'select',
                'config_categoryid',
                get_string('category', 'block_graderanking'),
                $items
            );
        } else {
            // If there are no categories, warning.
            $mform->addElement(
                'static',
                'description',
                get_string('alert', 'block_graderanking'),
                get_string('alert_no_categories', 'block_graderanking')
            );
        }

        // Decimal positions.
        $decimals = [0, 1, 2, 3, 4];
        $selectdecimals = $mform->addElement(
            'select',
            'config_decimals',
            get_string('decimals', 'block_graderanking'),
            $decimals
        );
        $selectdecimals->setSelected(0);

        // Name for the grade.
        $mform->addElement(
            'text',
            'config_gradename',
            get_string('gradename', 'block_graderanking')
        );
        $mform->setDefault('config_gradename', get_string('grade', 'block_graderanking'));
        $mform->setType('config_gradename', PARAM_TEXT);

        // Table height.
        $mform->addElement(
            'text',
            'config_tableheight',
            get_string('tableheight', 'block_graderanking')
        );
        $mform->setDefault('config_tableheight', 0);
        $mform->setType('config_tableheight', PARAM_INT);
    }
}
