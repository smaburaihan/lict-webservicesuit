<?php
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
 * Web service local plugin template external functions and service definitions.
 *
 * @package    local
 * @subpackage bs_webservicesuite
 * @author     Brain station 23 ltd <brainstation-23.com>
 * @copyright  2020 Brain station 23 ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// We defined the web service functions to install.
$functions = array(
    'lict_quiz_category_grade_report' => array(
        'classname' => 'local_lict_webservicesuite_external',
        'methodname' => 'quiz_category_grade_report',
        'classpath' => 'local/lict_webservicesuite/externallib.php',
        'description' => 'Return quiz category wise grade reports',
        'type' => 'read',
        'capabilities' => 'gradereport/user:view',
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),
);

