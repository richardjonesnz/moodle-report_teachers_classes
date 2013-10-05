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
 * Displays a list of all assignment files for a user and/or course
 *
 * @package     report
 * @subpackage  assignment_files
 * @author      Russell England <russell.england@gmail.com>
 * @copyright   Russell England <russell.england@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');
require_once(dirname(__FILE__) . '/filter_form.php');
require_once($CFG->libdir . '/adminlib.php');

$filters['page'] = optional_param('page', 0, PARAM_INT);
$filters['perpage'] = optional_param('perpage', 20, PARAM_INT);
$filters['userid'] = optional_param('userid', null, PARAM_INT);
$filters['courseid'] = optional_param('corseid', null, PARAM_INT);
$filters['username'] = optional_param('username', null, PARAM_TEXT);
$filters['coursename'] = optional_param('coursename', null, PARAM_TEXT);
$filters['assignmentname'] = optional_param('assignmentname', null, PARAM_TEXT);

require_login();

$context = context_system::instance();

require_capability('report/assignment_files:view', $context);

$heading = get_string('pluginname', 'report_assignment_files');
$PAGE->set_context($context);
$PAGE->set_heading(format_string($heading));
$PAGE->set_title(format_string($heading));
$PAGE->set_url('/report/assignment_files/index.php', $filters);
$PAGE->set_pagelayout('report');
admin_externalpage_setup('report_assignment_files');

echo $OUTPUT->header();
echo $OUTPUT->heading($heading);

$mform = new filter_form();

if ($formdata = $mform->get_data()) {
    // New filters so reset the page number.
    $filters['page'] = 0;
    $filters['userid'] = $formdata->userid;
    $filters['courseid'] = $formdata->courseid;
    $filters['username'] = $formdata->username;
    $filters['coursename'] = $formdata->coursename;
    $filters['assignmentname'] = $formdata->assignmentname;
} else {
    $formdata = new stdClass();
    $formdata->userid = $filters['userid'];
    $formdata->courseid = $filters['courseid'];
    $formdata->username = $filters['username'];
    $formdata->coursename = $filters['coursename'];
    $formdata->assignmentname = $filters['assignmentname'];
}

$mform->set_data($formdata);
$mform->display();

$totalcount = 0;
$assignments = assignment_files_get_list($filters, $totalcount);
$filters['totalcount'] = $totalcount;
echo assignment_files_display_list($assignments, $filters);

echo $OUTPUT->footer();