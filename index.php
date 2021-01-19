<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Displays a list of students in all courses of a given teacher.
 *
 * @package     report
 * @subpackage  teachers_classes
 * @author richard f jones richardnz@outlook.com
 * @copyright 2021 richard f jones <richardnz@outlook.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use report_teachers_classes\forms\teacher_form;
use report_teachers_classes\local\class_list;
require('../../config.php');

$teachername = optional_param('teachername', '', PARAM_ALPHA);
$context = context_system::instance();

$heading = get_string('pluginname', 'report_teachers_classes');
$PAGE->set_context($context);
$PAGE->set_heading(format_string($heading));
$PAGE->set_title(format_string($heading));
$PAGE->set_url('/report/teachers_classes/index.php', ['teachername' => $teachername]);

require_login();
require_capability('report/teachers_classes:view', $context);

$PAGE->set_pagelayout('report');

echo $OUTPUT->header();

$mform = new teacher_form();

if ($formdata = $mform->get_data()) {
    $teachername = $formdata->teachername;
}

if ($teachername === '') {
    $teachername = get_string('nodata', 'report_teachers_classes');
}

$mform->set_data($formdata);
$mform->display();

echo $OUTPUT->render(new class_list($heading, $teachername));

echo $OUTPUT->footer();