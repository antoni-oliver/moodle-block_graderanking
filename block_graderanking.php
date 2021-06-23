<?php
class block_graderanking extends block_base {
	public function init() {
		$this->title = get_string('graderanking', 'block_graderanking');
	}

	public function get_content() {
		global $PAGE;

		$PAGE->requires->js_call_amd('block_graderanking/graderanking', 'init');

		if ($this->content !== null) {
			return $this->content;
		} else {
			$this->refresh_content();
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

			if (empty($this->config->categoryid)) {
				$this->content->text = get_string('defaulttext', 'block_graderanking');
			} else {
				$this->refresh_content();
			}
		}
	}

	public function instance_allow_multiple() {
		  return true;
	}

	public function refresh_content() {
		global $CFG, $COURSE, $USER;
		require_once($CFG->libdir . '/grade/grade_category.php');
		require_once($CFG->libdir . '/grade/grade_item.php');
		require_once($CFG->libdir . '/grade/grade_grade.php');
		
		$this->content = new stdClass;

		$users = get_enrolled_users($this->context, 'mod/assignment:submit');
		$cat = grade_category::fetch(array('courseid' => $COURSE->id, 'id' => $this->config->categoryid));
		if (!$cat) {
			$this->content->text = "Cannot find category id `{$this->config->categoryid}`.";
			return;
		}
		$cat_item = $cat->get_grade_item();
		$items = grade_item::fetch_all(array('courseid' => $COURSE->id));
		$grades = grade_grade::fetch_all(array('courseid' => $COURSE->id, 'itemid' => $cat_item->id));

		$usergrades = array();

		# get grades as userid -> [grade => finalgrade]
		foreach ($grades as $grade) {
			if (array_key_exists($grade->userid, $users)) {
				$usergrades[$grade->userid] = array('grade' => $grade->finalgrade);
			}
		}

		# and add user info userid -> [firstname => firstname, lastname => lastname, grade => grade]
		foreach ($users as $userid => $user) {
			$usergrade = $usergrades[$userid];
			$usergrade['userid']	= $userid;
			$usergrade['firstname']	= $user->firstname;
			$usergrade['lastname']	= $user->lastname;
			$usergrade['grade'] 	= number_format($usergrade['grade'], $this->config->decimals);
			$usergrades[$userid] = $usergrade;
		}

		# sort
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

		# ranking
		for ($i = 0, $last_grade = NULL, $n = 1; $i < count($usergrades); $i++) {
			$usergrade = $usergrades[$i];
			if ($last_grade !== $usergrade['grade']) {
				$n = $i + 1;	
			}
			$usergrade['n'] = $n;
			$last_grade = $usergrade['grade'];
			$usergrades[$i] = $usergrade;
		}

		# table
		$tablebody = "";
		foreach ($usergrades as $grade) {
			$me = $grade['userid'] == $USER->id ? " class=\"me\"" : "";
			$n = $grade['n'];
			$fn = $grade['firstname'];
			$ln = $grade['lastname'];
			$g = $grade['grade'];
			$tablebody .= "<tr$me><td>$n</td><td><div class=\"studentname\">$fn $ln</div></td><td>$g</td></tr>";
		}

		$huser = get_string('participant', 'block_graderanking');
		$hgrade = $this->config->gradename;
		$tablehead = "<tr><th>#</th><th>$huser</th><th>$hgrade</th></tr>";

		$style = "";
		if ($this->config->tableheight) {
			$style .= "max-height: {$this->config->tableheight}px;\n";
			$style .= "overflow-y: scroll;\n";
		}

		$table = "<div class=\"graderanking\" style=\"$style\"><table class=\"generaltable table-sm\" width=\"100%\"><thead>$tablehead</thead><tbody>$tablebody</tbody></table></div>";
		$this->content->text = $table;
	}
}
