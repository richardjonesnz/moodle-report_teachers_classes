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
 * @subpackage  teachers_classes
 * @author richard f jones richardnz@outlook.com
 * @copyright 2021 richard f jones <richardnz@outlook.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_teachers_classes\forms;
use moodleform;

class teacher_form extends moodleform {

    public function definition() {
        $mform =& $this->_form;

        $mform->addElement('header', 'details', get_string('formheader', 'report_teachers_classes'));

        $mform->addElement('text', 'teachername', get_string('filter', 'report_teachers_classes'));
        $mform->setType('teachername', PARAM_ALPHA);

        $this->add_action_buttons(false, get_string('submit'));
    }
}