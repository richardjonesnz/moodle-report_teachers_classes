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
 * Report filter form
 *
 * @package     report
 * @subpackage  assignment_files
 * @author      Russell England <russell.england@gmail.com>
 * @copyright   Russell England <russell.england@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/formslib.php');

class filter_form extends moodleform {

    public function definition() {
        $mform =& $this->_form;

        $mform->addElement('header', 'details', get_string('filteroptions', 'report_assignment_files'));

        $mform->addElement('hidden', 'userid');
        $mform->setType('userid', PARAM_INT);

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('text', 'username', get_string('usernamefilter', 'report_assignment_files'));
        $mform->setType('username', PARAM_TEXT);

        $mform->addElement('text', 'coursename', get_string('coursenamefilter', 'report_assignment_files'));
        $mform->setType('coursename', PARAM_TEXT);

        $mform->addElement('text', 'assignmentname', get_string('assignmentnamefilter', 'report_assignment_files'));
        $mform->setType('assignmentname', PARAM_TEXT);

        // Allow the page index.
        $mform->addElement('advcheckbox', 'zipall', get_string('zipall', 'report_assignment_files'));
        $mform->setDefault('showindex', 0);

        $this->add_action_buttons(false, get_string('submit'));
    }

}
