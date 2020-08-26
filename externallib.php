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
 * External Web Service Template
 * @package local
 * @subpackage bs_webservicesuite
 * @author     Brain station 23 ltd <brainstation-23.com>
 * @copyright  2020 Brain station 23 ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir . "/externallib.php");
require_once($CFG->dirroot . "/course/externallib.php");
require_once ($CFG->dirroot.'/grade/report/user/externallib.php');

class local_lict_webservicesuite_external extends gradereport_user_external {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function quiz_category_grade_report_parameters() {
        return new external_function_parameters (
            array(
                'courseid' => new external_value(PARAM_INT, 'Course Id', VALUE_REQUIRED),
                'userid'   => new external_value(PARAM_INT, 'Return grades only for this user (optional)', VALUE_REQUIRED)
            )
        );
    }

    /**
     * @param string $idnumbers
     * @param string $tags
     * @return array
     * @throws dml_exception
     * @throws dml_missing_record_exception
     */
    public static function quiz_category_grade_report($courseid, $userid) {

        global $CFG, $USER;

        list($params, $course, $context, $user, $groupid) = self::check_report_access($courseid, $userid);
        $userid   = $params['userid'];

        // We pass userid because it can be still 0.
        list($gradeitems, $warnings) = self::get_report_data($course, $context, $user, $userid, $groupid, false);

        foreach ($gradeitems as &$gradeitem) {
            if (isset($gradeitem['feedback']) and isset($gradeitem['feedbackformat'])) {
                list($gradeitem['feedback'], $gradeitem['feedbackformat']) =
                    external_format_text($gradeitem['feedback'], $gradeitem['feedbackformat'], $context->id);
            }
            foreach ($gradeitem['gradeitems'] as &$gitem):
                if ($gitem['itemmodule'] =='quiz'){
                    $gitem['categories_grades'] = self::get_quiz_question_category_grade_details($gitem['iteminstance'],$userid);
                }
            endforeach;
        }

        $result = array();
        $result['usergrades'] = $gradeitems;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function quiz_category_grade_report_returns() {
        return new external_single_structure(
            array(
                'usergrades' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'courseid' => new external_value(PARAM_INT, 'course id'),
                            'userid'   => new external_value(PARAM_INT, 'user id'),
                            'userfullname' => new external_value(PARAM_TEXT, 'user fullname'),
                            'maxdepth'   => new external_value(PARAM_INT, 'table max depth (needed for printing it)'),
                            'gradeitems' => new external_multiple_structure(
                                new external_single_structure(
                                    array(
                                        'id' => new external_value(PARAM_INT, 'Grade item id'),
                                        'itemname' => new external_value(PARAM_TEXT, 'Grade item name'),
                                        'itemtype' => new external_value(PARAM_ALPHA, 'Grade item type'),
                                        'itemmodule' => new external_value(PARAM_PLUGIN, 'Grade item module'),
                                        'iteminstance' => new external_value(PARAM_INT, 'Grade item instance'),
                                        'itemnumber' => new external_value(PARAM_INT, 'Grade item item number'),
                                        'categoryid' => new external_value(PARAM_INT, 'Grade item category id'),
                                        'outcomeid' => new external_value(PARAM_INT, 'Outcome id'),
                                        'scaleid' => new external_value(PARAM_INT, 'Scale id'),
                                        'locked' => new external_value(PARAM_BOOL, 'Grade item for user locked?', VALUE_OPTIONAL),
                                        'cmid' => new external_value(PARAM_INT, 'Course module id (if type mod)', VALUE_OPTIONAL),
                                        'weightraw' => new external_value(PARAM_FLOAT, 'Weight raw', VALUE_OPTIONAL),
                                        'weightformatted' => new external_value(PARAM_NOTAGS, 'Weight', VALUE_OPTIONAL),
                                        'status' => new external_value(PARAM_ALPHA, 'Status', VALUE_OPTIONAL),
                                        'graderaw' => new external_value(PARAM_FLOAT, 'Grade raw', VALUE_OPTIONAL),
                                        'gradedatesubmitted' => new external_value(PARAM_INT, 'Grade submit date', VALUE_OPTIONAL),
                                        'gradedategraded' => new external_value(PARAM_INT, 'Grade graded date', VALUE_OPTIONAL),
                                        'gradehiddenbydate' => new external_value(PARAM_BOOL, 'Grade hidden by date?', VALUE_OPTIONAL),
                                        'gradeneedsupdate' => new external_value(PARAM_BOOL, 'Grade needs update?', VALUE_OPTIONAL),
                                        'gradeishidden' => new external_value(PARAM_BOOL, 'Grade is hidden?', VALUE_OPTIONAL),
                                        'gradeislocked' => new external_value(PARAM_BOOL, 'Grade is locked?', VALUE_OPTIONAL),
                                        'gradeisoverridden' => new external_value(PARAM_BOOL, 'Grade overridden?', VALUE_OPTIONAL),
                                        'gradeformatted' => new external_value(PARAM_NOTAGS, 'The grade formatted', VALUE_OPTIONAL),
                                        'grademin' => new external_value(PARAM_FLOAT, 'Grade min', VALUE_OPTIONAL),
                                        'grademax' => new external_value(PARAM_FLOAT, 'Grade max', VALUE_OPTIONAL),
                                        'rangeformatted' => new external_value(PARAM_NOTAGS, 'Range formatted', VALUE_OPTIONAL),
                                        'percentageformatted' => new external_value(PARAM_NOTAGS, 'Percentage', VALUE_OPTIONAL),
                                        'lettergradeformatted' => new external_value(PARAM_NOTAGS, 'Letter grade', VALUE_OPTIONAL),
                                        'rank' => new external_value(PARAM_INT, 'Rank in the course', VALUE_OPTIONAL),
                                        'numusers' => new external_value(PARAM_INT, 'Num users in course', VALUE_OPTIONAL),
                                        'averageformatted' => new external_value(PARAM_NOTAGS, 'Grade average', VALUE_OPTIONAL),
                                        'feedback' => new external_value(PARAM_RAW, 'Grade feedback', VALUE_OPTIONAL),
                                        'feedbackformat' => new external_format_value('feedback', VALUE_OPTIONAL),
                                        'categories_grades' => new external_multiple_structure(
                                            new external_single_structure(
                                                array(
                                                    'category' => new external_value(PARAM_INT, 'Question bank Category id'),
                                                    'name' => new external_value(PARAM_TEXT, 'Question bank Category name'),
                                                    'question_count' => new external_value(PARAM_INT, 'Number of question in this category'),
                                                    'success_count' => new external_value(PARAM_INT, 'Percentage of right answer in this category.'),
                                                    'fail_count' => new external_value(PARAM_INT, 'Percentage of right answer in this category.'),

                                                ), 'Grade items'
                                            ), 'Quiz ids'
                                        ,VALUE_OPTIONAL,null)
                                    ), 'Grade items'
                                )
                            )
                        )
                    )
                ),
                'warnings' => new external_warnings()
            )
        );
    }

    private static function get_quiz_question_category_grade_details($quiz_id, $student_id)
    {
        global $DB;

        $sql = 'SELECT  mq.category, mqc.name, COUNT(mqa.id) AS question_count,
        SUM( CASE WHEN (SELECT fraction FROM {question_attempt_steps} WHERE questionattemptid = mqa.id ORDER BY timecreated DESC LIMIT 1 OFFSET 0 ) * mqa.maxmark > 0 THEN 1 ELSE 0 END ) AS success_count, 
        SUM( CASE WHEN (SELECT fraction FROM {question_attempt_steps} WHERE questionattemptid = mqa.id ORDER BY timecreated DESC LIMIT 1 OFFSET 0 ) * mqa.maxmark <= 0 THEN 1 ELSE 0 END ) AS fail_count
        FROM {question_attempts} AS mqa
        LEFT JOIN {question} AS mq ON mqa.questionid = mq.id
        LEFT JOIN {question_categories} AS mqc ON mq.category = mqc.id WHERE mqa.questionusageid IN (
        SELECT uniqueid FROM {quiz_attempts} WHERE userid= :student_id AND quiz = :quiz_id) GROUP BY mq.category';
        $response_attempt_grade =  $DB->get_records_sql($sql,
        [
            'student_id' =>$student_id,
            'quiz_id' =>$quiz_id,
        ]);
//
//        array_map(function ($item){
//            $item->suc_per = is_null($item->suc_per)?0.00:$item->suc_per;
//        },$response_attempt_grade);
        return $response_attempt_grade;


    }

}
