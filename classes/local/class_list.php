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
 * Return a teacher and all the courses they have that role in.
 *
 * @package     report
 * @subpackage  teachers_classes
 * @author richard f jones richardnz@outlook.com
 * @copyright 2021 richard f jones <richardnz@outlook.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_teachers_classes\local;
use renderable;
use renderer_base;
use templatable;
use stdClass;
class class_list implements renderable, templatable {

    protected $heading;
    protected $teachername;

    public function __construct($heading, $teachername) {

        $this->heading = $heading;
        $this->teachername = $teachername;
    }

    protected function validate_teacher() {
        global $DB;

        // Set up the expression for partial matches to the input form.
        $namefilter = '%' . $this->teachername . '%';
        $sql = "SELECT ra.id AS id, c.id AS courseid, c.fullname, u.id AS userid, u.firstname, u.lastname, ra.roleid
                  FROM mdl_course AS c
                  JOIN mdl_context AS ctx ON c.id = ctx.instanceid
                  JOIN mdl_role_assignments AS ra ON ra.contextid = ctx.id
                  JOIN mdl_user AS u ON u.id = ra.userid
                 WHERE (ra.roleid = :r1 OR ra.roleid = :r2)
                   AND u.lastname LIKE '$namefilter'";

        $courses = $DB->get_records_sql($sql, ['r1' => 3, 'r2' => 4]);
        var_dump($courses);

        // Check that some courses are returned.
        if (!$courses) {
            return get_string('nodata', 'report_teachers_classes');
        }

        // Check only one teacher returned.
        $userids = array();
        foreach ($courses as $course) {
            $userids[] = $course->userid;
        }

        if (count(array_unique($userids)) != 1) {
            return get_string('narrowsearch', 'report_teachers_classes');
        }

        return $courses;

    }

    function export_for_template(renderer_base $output) {

        $courses = self::validate_teacher();

        $table = new stdClass();
        // Heading
        $table->heading = $this->heading;
        // Table headers.
        $table->tableheaders = [
            get_string('firstname', 'report_teachers_classes'),
            get_string('lastname', 'report_teachers_classes'),
            get_string('courseid', 'report_teachers_classes'),
            get_string('fullname', 'report_teachers_classes'),
        ];

        if (!is_string($courses)) {

            // Build the data rows.
            foreach ($courses as $course) {
                $data = array();
                $data[] = $course->firstname;
                $data[] = $course->lastname;
                $data[] = $course->courseid;
                $data[] = $course->fullname;
                $table->tabledata[] = $data;
            }

            $table->message = get_string('searchresults', 'report_teachers_classes');

        } else {

            $table->message = $courses;
            $table->tabledata = null;
        }

        return $table;
    }
}