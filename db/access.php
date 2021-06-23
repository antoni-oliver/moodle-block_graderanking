<?php
$capabilities = array(
	'block/graderanking:addinstance' => array(
		'riskbitmask' => RISK_SPAM | RISK_XSS,

		'captype' => 'write',
		'contextlevel' => CONTEXT_BLOCK,
		'archetypes' => array(
			'editingteacher' => CAP_ALLOW,
			'manager' => CAP_ALLOW
		),

		'clonepermissionsfrom' => 'moodle/site:manageblocks'
	),
);
