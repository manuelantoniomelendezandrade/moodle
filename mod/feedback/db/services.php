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
 * Feedback external functions and service definitions.
 *
 * @package    mod_feedback
 * @category   external
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.3
 */

defined('MOODLE_INTERNAL') || die;

$functions = array(

    'mod_feedback_get_feedbacks_by_courses' => array(
        'classname'     => 'mod_feedback_external',
        'methodname'    => 'get_feedbacks_by_courses',
        'description'   => 'Returns a list of feedbacks in a provided list of courses, if no list is provided all feedbacks that
                            the user can view will be returned.',
        'type'          => 'read',
        'capabilities'  => 'mod/feedback:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),
    'mod_feedback_get_feedback_access_information' => array(
        'classname'     => 'mod_feedback_external',
        'methodname'    => 'get_feedback_access_information',
        'description'   => 'Return access information for a given feedback.',
        'type'          => 'read',
        'capabilities'  => 'mod/feedback:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),
    'mod_feedback_view_feedback' => array(
        'classname'     => 'mod_feedback_external',
        'methodname'    => 'view_feedback',
        'description'   => 'Trigger the course module viewed event and update the module completion status.',
        'type'          => 'write',
        'capabilities'  => 'mod/feedback:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_feedback_get_current_completed_tmp' => array(
        'classname'     => 'mod_feedback_external',
        'methodname'    => 'get_current_completed_tmp',
        'description'   => 'Returns the temporary completion record for the current user.',
        'type'          => 'read',
        'capabilities'  => 'mod/feedback:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_feedback_get_items' => array(
        'classname'     => 'mod_feedback_external',
        'methodname'    => 'get_items',
        'description'   => 'Returns the items (questions) in the given feedback.',
        'type'          => 'read',
        'capabilities'  => 'mod/feedback:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_feedback_launch_feedback' => array(
        'classname'     => 'mod_feedback_external',
        'methodname'    => 'launch_feedback',
        'description'   => 'Starts or continues a feedback submission.',
        'type'          => 'write',
        'capabilities'  => 'mod/feedback:complete',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_feedback_get_page_items' => array(
        'classname'     => 'mod_feedback_external',
        'methodname'    => 'get_page_items',
        'description'   => 'Get a single feedback page items.',
        'type'          => 'read',
        'capabilities'  => 'mod/feedback:complete',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
);