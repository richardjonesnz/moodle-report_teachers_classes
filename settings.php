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
 * Links and settings
 *
 * Contains settings used by assignment files report.
 * @package     report
 * @subpackage  teachers_classes
 * @author richard f jones richardnz@outlook.com
 * @copyright 2021 richard f jones <richardnz@outlook.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

// Just a link to course report.
$ADMIN->add('reports', new admin_externalpage('report_teachers_classes',
        get_string('pluginname', 'report_teachers_classes'),
        "$CFG->wwwroot/report/teachers_classes",
        'report/teachers_classes:view'));

// No report settings.
$settings = null;
