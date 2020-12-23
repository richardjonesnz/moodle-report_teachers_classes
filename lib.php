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
 * Library of functions and constants for the assignment_files report
 *
 * @package     report
 * @subpackage  assignment_files
 * @author      Russell England <russell.england@gmail.com>
 * @copyright   Russell England <russell.england@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

// Required for definitions.
require_once($CFG->dirroot . '/mod/assign/submissionplugin.php');
require_once($CFG->dirroot . '/mod/assign/submission/file/locallib.php');
// Flexible table.
require_once($CFG->libdir . '/tablelib.php');

/**
 * Returns a list of coursefiles for a user
 *
 * @global object $DB
 * @param array $filters
 * @param int $totalcount
 * @return array $assignments - list of assignments and filenames
 */
function assignment_files_get_list($filters, &$totalcount) {
    global $DB;

    $params = array();
    $wheres = array();
    $where = '';

    if (isset($filters['userid']) && $filters['userid']) {
        $params['userid'] = $filters['userid'];
        $wheres[] = 'u.id = :userid';
    }
    if (isset($filters['username']) && $filters['username']) {
        $params['username'] = '%' . $DB->sql_like_escape($filters['username']) . '%';
        $wheres[] = $DB->sql_like('u.username', ':username', false);
    }
    if (isset($filters['courseid']) && $filters['courseid']) {
        $params['courseid'] = $filters['courseid'];
        $wheres[] = 'c.id = :courseid';
    }
    if (isset($filters['coursename']) && $filters['coursename']) {
        $params['coursename'] = '%' . $DB->sql_like_escape($filters['coursename']) . '%';
        $wheres[] = $DB->sql_like('c.fullname', ':coursename', false);
    }
    if (isset($filters['assignmentid']) && $filters['assignmentid']) {
        $params['assignmentid'] = $filters['assignmentid'];
        $wheres[] = 'a.id = :assignmentid';
    }
    if (isset($filters['assignmentname']) && $filters['assignmentname']) {
        $params['assignmentname'] = '%' . $DB->sql_like_escape($filters['assignmentname']) . '%';
        $wheres[] = $DB->sql_like('a.name', ':assignmentname', false);
    }

    if (!empty($wheres)) {
        $where = 'WHERE ' . implode(' AND ', $wheres);
    }

    $sqlbody = "FROM {assignsubmission_file} f
                JOIN {assign_submission} s ON s.id = f.submission
                JOIN {assign} a ON a.id = f.assignment
                JOIN {course} c ON c.id = a.course
                JOIN {user} u ON u.id = s.userid
                {$where}";

    $sql = "SELECT f.id AS fileid,
            s.id AS submissionid,
            a.id AS assignmentid,
            a.name AS assignmentname,
            c.id AS courseid,
            c.fullname AS coursename,
            u.id AS userid,
            u.username AS username
            {$sqlbody}
            ORDER BY u.username, c.fullname, a.name";

    $sqlcount = "SELECT COUNT(*) " . $sqlbody;
    $totalcount = $DB->count_records_sql($sqlcount, $params);

    $offset = $filters['page'] * $filters['perpage'];

    $submissions = $DB->get_records_sql($sql, $params, $offset, $filters['perpage']);

    $fs = get_file_storage();
    foreach ($submissions as $submission) {
        $cm = get_coursemodule_from_instance('assign', $submission->assignmentid, $submission->courseid);
        $submission->cmid = $cm->id;
        $context = context_module::instance($cm->id);
        $files = $fs->get_area_files($context->id, 'assignsubmission_file',
                ASSIGNSUBMISSION_FILE_FILEAREA, $submission->submissionid, 'filename', false);
        // TODO filter for filename.
        $submission->files = $files;
    }

    return $submissions;
}

/**
 * Displays a table of assignment files
 *
 * @param array $submissions array of record objects
 * @param array $params - contains paging parameters
 */
function assignment_files_display_list($submissions, $params) {
    $table = new flexible_table('view-assignment-files');
    $table->define_columns(array(
        'username',
        'coursename',
        'assignmentname',
        'filenames'
    ));
    $table->define_headers(array(
        get_string('username'),
        get_string('course'),
        get_string('assignmentname', 'report_assignment_files'),
        get_string('filenames', 'report_assignment_files')
    ));

    $table->column_class('username', 'username');
    $table->column_class('coursename', 'coursename');
    $table->column_class('assignmentname', 'assignmentname');
    $table->column_class('filenames', 'filenames');

    $table->define_baseurl(new moodle_url('/report/assignment_files/index.php', $params));
    $table->sortable(false);
    $table->collapsible(false);

    $table->set_attribute('cellspacing', '0');
    $table->set_attribute('id', 'view-assignment-files');
    $table->set_attribute('class', 'generaltable');
    $table->set_attribute('width', '100%');
    $table->setup();

    $table->initialbars($params['totalcount'] > $params['perpage']);
    // Something weird going on with pagesize() it prints blank rows up to the size of the perpage.
    $perpage = $params['totalcount'] > $params['perpage'] ? $params['perpage'] : $params['totalcount'];
    $table->pagesize($perpage, $params['totalcount']);

    if ($submissions) {

        foreach ($submissions as $submission) {
            $row = array();

            $userurl = new moodle_url('/user/view.php', array('id' => $submission->userid));
            $row[] = html_writer::link($userurl, format_string($submission->username));

            $courseurl = new moodle_url('/course/view.php', array('id' => $submission->courseid));
            $row[] = html_writer::link($courseurl, format_string($submission->coursename));

            $assignmenturl = new moodle_url('/mod/assign/view.php', array('id' => $submission->cmid));
            $row[] = html_writer::link($assignmenturl, format_string($submission->assignmentname));

            $files = '';
            foreach ($submission->files as $file) {
                $files .= format_string($file->get_filename());
                $files .= html_writer::empty_tag('br');
            }
            $row[] = $files;

            $table->add_data($row);
        }
    }

    $table->print_html();
}

/**
 * Downloads a zipfile with all submissions in it.
 */
function assignment_files_download($submissions) {

    // Create a ziparchive file.
    $ziparchive = new \zip_archive();
    $zipfilepath = './test_' . userdate(time(), '%Y%m%d%H%M%S') . '.zip';

    $ziparchive->open($zipfilepath, \file_archive::CREATE);

    foreach ($submissions as $submission) {
        foreach($submission->files as $file) {
            $filename = $file->get_filename();
            $content = $file->get_content();
            $ziparchive->add_file_from_string($filename, $content);
        }
    }

    $ziparchive->close();
}