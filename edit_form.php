<?php
class block_graderanking_edit_form extends block_edit_form {
	protected function specific_definition($mform) {
		global $CFG, $COURSE;
		
		require_once($CFG->libdir . '/grade/grade_category.php');

		$mform->addElement('static', 'description', get_string('alert', 'block_graderanking'), get_string('alert_content', 'block_graderanking'));

		// Section header title according to language file.
		$mform->addElement('header', 'config_header', get_string('blocksettings', 'block'));

		// Title
		$mform->addElement('text', 'config_title', get_string('blocktitle', 'block_graderanking'));
		$mform->setDefault('config_title', get_string('title', 'block_graderanking'));
		$mform->setType('config_title', PARAM_TEXT);

		// Category
		$cats = grade_category::fetch_all(array('courseid' => $COURSE->id));
		if ($cats) {
			$items = array();
			foreach ($cats as $id => $cat) {
				$items[$id] = $cat->fullname;
			}
			$mform->addElement('select', 'config_categoryid', get_string('category', 'block_graderanking'), $items);
		} else {
			$mform->addElement('static', 'description', get_string('alert', 'block_graderanking'), get_string('alert_no_categories', 'block_graderanking'));
		}
		
		// Decimal positions
		$decimals = array(0, 1, 2, 3, 4);
		$selectDecimals = $mform->addElement('select', 'config_decimals', get_string('decimals', 'block_graderanking'), $decimals);
		$selectDecimals->setSelected(0);

		// Name for the grade
		$mform->addElement('text', 'config_gradename', get_string('gradename', 'block_graderanking'));
		$mform->setDefault('config_gradename', get_string('grade', 'block_graderanking'));
		$mform->setType('config_gradename', PARAM_TEXT);

		// Table height
		$mform->addElement('text', 'config_tableheight', get_string('tableheight', 'block_graderanking'));
		$mform->setDefault('config_tableheight', 0);
		$mform->setType('config_tableheight', PARAM_INT);
	}
}
