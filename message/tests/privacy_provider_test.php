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
 * Privacy provider tests.
 *
 * @package    core_message
 * @copyright  2018 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_privacy\local\metadata\collection;
use core_message\privacy\provider;
use \core_privacy\local\request\writer;
use \core_privacy\local\request\transform;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy provider tests class.
 *
 * @package    core_message
 * @copyright  2018 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_message_privacy_provider_testcase extends \core_privacy\tests\provider_testcase {

    /**
     * Test for provider::get_metadata().
     */
    public function test_get_metadata() {
        $collection = new collection('core_message');
        $newcollection = provider::get_metadata($collection);
        $itemcollection = $newcollection->get_collection();
        $this->assertCount(8, $itemcollection);

        $messagestable = array_shift($itemcollection);
        $this->assertEquals('messages', $messagestable->get_name());

        $messageuseractionstable = array_shift($itemcollection);
        $this->assertEquals('message_user_actions', $messageuseractionstable->get_name());

        $messageconversationmemberstable = array_shift($itemcollection);
        $this->assertEquals('message_conversation_members', $messageconversationmemberstable->get_name());

        $messagecontacts = array_shift($itemcollection);
        $this->assertEquals('message_contacts', $messagecontacts->get_name());

        $messagecontactrequests = array_shift($itemcollection);
        $this->assertEquals('message_contact_requests', $messagecontactrequests->get_name());

        $messageusersblocked = array_shift($itemcollection);
        $this->assertEquals('message_users_blocked', $messageusersblocked->get_name());

        $notificationstable = array_shift($itemcollection);
        $this->assertEquals('notifications', $notificationstable->get_name());

        $usersettings = array_shift($itemcollection);
        $this->assertEquals('core_message_messageprovider_settings', $usersettings->get_name());

        $privacyfields = $messagestable->get_privacy_fields();
        $this->assertArrayHasKey('useridfrom', $privacyfields);
        $this->assertArrayHasKey('conversationid', $privacyfields);
        $this->assertArrayHasKey('subject', $privacyfields);
        $this->assertArrayHasKey('fullmessage', $privacyfields);
        $this->assertArrayHasKey('fullmessageformat', $privacyfields);
        $this->assertArrayHasKey('fullmessagehtml', $privacyfields);
        $this->assertArrayHasKey('smallmessage', $privacyfields);
        $this->assertArrayHasKey('timecreated', $privacyfields);
        $this->assertEquals('privacy:metadata:messages', $messagestable->get_summary());

        $privacyfields = $messageuseractionstable->get_privacy_fields();
        $this->assertArrayHasKey('userid', $privacyfields);
        $this->assertArrayHasKey('messageid', $privacyfields);
        $this->assertArrayHasKey('action', $privacyfields);
        $this->assertArrayHasKey('timecreated', $privacyfields);
        $this->assertEquals('privacy:metadata:message_user_actions', $messageuseractionstable->get_summary());

        $privacyfields = $messageconversationmemberstable->get_privacy_fields();
        $this->assertArrayHasKey('conversationid', $privacyfields);
        $this->assertArrayHasKey('userid', $privacyfields);
        $this->assertArrayHasKey('timecreated', $privacyfields);
        $this->assertEquals('privacy:metadata:message_conversation_members', $messageconversationmemberstable->get_summary());

        $privacyfields = $messagecontacts->get_privacy_fields();
        $this->assertArrayHasKey('userid', $privacyfields);
        $this->assertArrayHasKey('contactid', $privacyfields);
        $this->assertArrayHasKey('timecreated', $privacyfields);
        $this->assertEquals('privacy:metadata:message_contacts', $messagecontacts->get_summary());

        $privacyfields = $messagecontactrequests->get_privacy_fields();
        $this->assertArrayHasKey('userid', $privacyfields);
        $this->assertArrayHasKey('requesteduserid', $privacyfields);
        $this->assertArrayHasKey('timecreated', $privacyfields);
        $this->assertEquals('privacy:metadata:message_contact_requests', $messagecontactrequests->get_summary());

        $privacyfields = $messageusersblocked->get_privacy_fields();
        $this->assertArrayHasKey('userid', $privacyfields);
        $this->assertArrayHasKey('blockeduserid', $privacyfields);
        $this->assertArrayHasKey('timecreated', $privacyfields);
        $this->assertEquals('privacy:metadata:message_users_blocked', $messageusersblocked->get_summary());

        $privacyfields = $notificationstable->get_privacy_fields();
        $this->assertArrayHasKey('useridfrom', $privacyfields);
        $this->assertArrayHasKey('useridto', $privacyfields);
        $this->assertArrayHasKey('subject', $privacyfields);
        $this->assertArrayHasKey('fullmessage', $privacyfields);
        $this->assertArrayHasKey('fullmessageformat', $privacyfields);
        $this->assertArrayHasKey('fullmessagehtml', $privacyfields);
        $this->assertArrayHasKey('smallmessage', $privacyfields);
        $this->assertArrayHasKey('component', $privacyfields);
        $this->assertArrayHasKey('eventtype', $privacyfields);
        $this->assertArrayHasKey('contexturl', $privacyfields);
        $this->assertArrayHasKey('contexturlname', $privacyfields);
        $this->assertArrayHasKey('timeread', $privacyfields);
        $this->assertArrayHasKey('timecreated', $privacyfields);
        $this->assertEquals('privacy:metadata:notifications', $notificationstable->get_summary());
    }

    /**
     * Test for provider::export_user_preferences().
     */
    public function test_export_user_preferences_no_pref() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        provider::export_user_preferences($user->id);

        $writer = writer::with_context(\context_system::instance());

        $this->assertFalse($writer->has_any_data());
    }

    /**
     * Test for provider::export_user_preferences().
     */
    public function test_export_user_preferences() {
        global $USER;

        $this->resetAfterTest();

        $this->setAdminUser();

        // Create another user to set a preference for who we won't be exporting.
        $user = $this->getDataGenerator()->create_user();

        // Set some message user preferences.
        set_user_preference('message_provider_moodle_instantmessage_loggedin', 'airnotifier', $USER->id);
        set_user_preference('message_provider_moodle_instantmessage_loggedoff', 'popup', $USER->id);
        set_user_preference('message_blocknoncontacts', \core_message\api::MESSAGE_PRIVACY_ONLYCONTACTS, $USER->id);
        set_user_preference('message_provider_moodle_instantmessage_loggedoff', 'inbound', $user->id);

        // Set an unrelated preference.
        set_user_preference('some_unrelated_preference', 'courses', $USER->id);

        provider::export_user_preferences($USER->id);

        $writer = writer::with_context(\context_system::instance());

        $this->assertTrue($writer->has_any_data());

        $prefs = (array) $writer->get_user_preferences('core_message');

        // Check only 3 preferences exist.
        $this->assertCount(3, $prefs);
        $this->assertArrayHasKey('message_provider_moodle_instantmessage_loggedin', $prefs);
        $this->assertArrayHasKey('message_provider_moodle_instantmessage_loggedoff', $prefs);
        $this->assertArrayHasKey('message_blocknoncontacts', $prefs);

        foreach ($prefs as $key => $pref) {
            if ($key == 'message_provider_moodle_instantmessage_loggedin') {
                $this->assertEquals('airnotifier', $pref->value);
            } else if ($key == 'message_provider_moodle_instantmessage_loggedoff') {
                $this->assertEquals('popup', $pref->value);
            } else {
                $this->assertEquals(1, $pref->value);
            }
        }
    }

    /**
     * Test for provider::get_contexts_for_userid().
     */
    public function test_get_contexts_for_userid() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $contextlist = provider::get_contexts_for_userid($user->id);
        $this->assertCount(1, $contextlist);
        $contextforuser = $contextlist->current();
        $this->assertEquals(SYSCONTEXTID, $contextforuser->id);
    }

    /**
     * Test for provider::export_user_data().
     */
    public function test_export_for_context_with_contacts() {
        $this->resetAfterTest();

        // Create users to test with.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();

        \core_message\api::add_contact($user1->id, $user2->id);
        \core_message\api::add_contact($user1->id, $user3->id);
        \core_message\api::add_contact($user1->id, $user4->id);

        $this->export_context_data_for_user($user1->id, \context_system::instance(), 'core_message');

        $writer = writer::with_context(\context_system::instance());

        $contacts = (array) $writer->get_data([get_string('contacts', 'core_message')]);
        usort($contacts, ['static', 'sort_contacts']);

        $this->assertCount(3, $contacts);

        $contact1 = array_shift($contacts);
        $this->assertEquals($user2->id, $contact1->contact);

        $contact2 = array_shift($contacts);
        $this->assertEquals($user3->id, $contact2->contact);

        $contact3 = array_shift($contacts);
        $this->assertEquals($user4->id, $contact3->contact);
    }

    /**
     * Test for provider::export_user_data().
     */
    public function test_export_for_context_with_contact_requests() {
        $this->resetAfterTest();

        // Create users to test with.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();

        \core_message\api::create_contact_request($user1->id, $user2->id);
        \core_message\api::create_contact_request($user3->id, $user1->id);
        \core_message\api::create_contact_request($user1->id, $user4->id);

        $this->export_context_data_for_user($user1->id, \context_system::instance(), 'core_message');

        $writer = writer::with_context(\context_system::instance());

        $contactrequests = (array) $writer->get_data([get_string('contactrequests', 'core_message')]);

        $this->assertCount(3, $contactrequests);

        $contactrequest1 = array_shift($contactrequests);
        $this->assertEquals($user2->id, $contactrequest1->contactrequest);
        $this->assertEquals(get_string('yes'), $contactrequest1->maderequest);

        $contactrequest2 = array_shift($contactrequests);
        $this->assertEquals($user3->id, $contactrequest2->contactrequest);
        $this->assertEquals(get_string('no'), $contactrequest2->maderequest);

        $contactrequest3 = array_shift($contactrequests);
        $this->assertEquals($user4->id, $contactrequest3->contactrequest);
        $this->assertEquals(get_string('yes'), $contactrequest3->maderequest);
    }

    /**
     * Test for provider::export_user_data().
     */
    public function test_export_for_context_with_blocked_users() {
        $this->resetAfterTest();

        // Create users to test with.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();

        \core_message\api::block_user($user1->id, $user2->id);
        \core_message\api::block_user($user1->id, $user3->id);
        \core_message\api::block_user($user1->id, $user4->id);

        $this->export_context_data_for_user($user1->id, \context_system::instance(), 'core_message');

        $writer = writer::with_context(\context_system::instance());

        $blockedusers = (array) $writer->get_data([get_string('blockedusers', 'core_message')]);

        $this->assertCount(3, $blockedusers);

        $blockeduser1 = array_shift($blockedusers);
        $this->assertEquals($user2->id, $blockeduser1->blockeduser);

        $blockeduser2 = array_shift($blockedusers);
        $this->assertEquals($user3->id, $blockeduser2->blockeduser);

        $blockeduser3 = array_shift($blockedusers);
        $this->assertEquals($user4->id, $blockeduser3->blockeduser);
    }

    /**
     * Test for provider::export_user_data().
     */
    public function test_export_for_context_with_messages() {
        global $DB;

        $this->resetAfterTest();

        // Create users to test with.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        $now = time();

        // Send messages from user 1 to user 2.
        $m1 = $this->create_message($user1->id, $user2->id, $now - (9 * DAYSECS), true);
        $m2 = $this->create_message($user2->id, $user1->id, $now - (8 * DAYSECS));
        $m3 = $this->create_message($user1->id, $user2->id, $now - (7 * DAYSECS));

        // Send messages from user 3 to user 1.
        $m4 = $this->create_message($user3->id, $user1->id, $now - (6 * DAYSECS), true);
        $m5 = $this->create_message($user1->id, $user3->id, $now - (5 * DAYSECS));
        $m6 = $this->create_message($user3->id, $user1->id, $now - (4 * DAYSECS));

        // Send messages from user 3 to user 2 - these should not be included in the export.
        $m7 = $this->create_message($user3->id, $user2->id, $now - (3 * DAYSECS), true);
        $m8 = $this->create_message($user2->id, $user3->id, $now - (2 * DAYSECS));
        $m9 = $this->create_message($user3->id, $user2->id, $now - (1 * DAYSECS));

        // Mark message 2 and 5 as deleted.
        \core_message\api::delete_message($user1->id, $m2);
        \core_message\api::delete_message($user1->id, $m5);

        $this->export_context_data_for_user($user1->id, \context_system::instance(), 'core_message');

        $writer = writer::with_context(\context_system::instance());

        $this->assertTrue($writer->has_any_data());

        // Confirm the messages with user 2 are correct.
        $messages = (array) $writer->get_data([get_string('messages', 'core_message'), fullname($user2)]);
        $this->assertCount(3, $messages);

        $dbm1 = $DB->get_record('messages', ['id' => $m1]);
        $dbm2 = $DB->get_record('messages', ['id' => $m2]);
        $dbm3 = $DB->get_record('messages', ['id' => $m3]);

        usort($messages, ['static', 'sort_messages']);
        $m1 = array_shift($messages);
        $m2 = array_shift($messages);
        $m3 = array_shift($messages);

        $this->assertEquals(get_string('yes'), $m1->sender);
        $this->assertEquals(message_format_message_text($dbm1), $m1->message);
        $this->assertEquals(transform::datetime($now - (9 * DAYSECS)), $m1->timecreated);
        $this->assertNotEquals('-', $m1->timeread);
        $this->assertArrayNotHasKey('timedeleted', (array) $m1);

        $this->assertEquals(get_string('no'), $m2->sender);
        $this->assertEquals(message_format_message_text($dbm2), $m2->message);
        $this->assertEquals(transform::datetime($now - (8 * DAYSECS)), $m2->timecreated);
        $this->assertEquals('-', $m2->timeread);
        $this->assertArrayHasKey('timedeleted', (array) $m2);

        $this->assertEquals(get_string('yes'), $m3->sender);
        $this->assertEquals(message_format_message_text($dbm3), $m3->message);
        $this->assertEquals(transform::datetime($now - (7 * DAYSECS)), $m3->timecreated);
        $this->assertEquals('-', $m3->timeread);

        // Confirm the messages with user 3 are correct.
        $messages = (array) $writer->get_data([get_string('messages', 'core_message'), fullname($user3)]);
        $this->assertCount(3, $messages);

        $dbm4 = $DB->get_record('messages', ['id' => $m4]);
        $dbm5 = $DB->get_record('messages', ['id' => $m5]);
        $dbm6 = $DB->get_record('messages', ['id' => $m6]);

        usort($messages, ['static', 'sort_messages']);
        $m4 = array_shift($messages);
        $m5 = array_shift($messages);
        $m6 = array_shift($messages);

        $this->assertEquals(get_string('no'), $m4->sender);
        $this->assertEquals(message_format_message_text($dbm4), $m4->message);
        $this->assertEquals(transform::datetime($now - (6 * DAYSECS)), $m4->timecreated);
        $this->assertNotEquals('-', $m4->timeread);
        $this->assertArrayNotHasKey('timedeleted', (array) $m4);

        $this->assertEquals(get_string('yes'), $m5->sender);
        $this->assertEquals(message_format_message_text($dbm5), $m5->message);
        $this->assertEquals(transform::datetime($now - (5 * DAYSECS)), $m5->timecreated);
        $this->assertEquals('-', $m5->timeread);
        $this->assertArrayHasKey('timedeleted', (array) $m5);

        $this->assertEquals(get_string('no'), $m6->sender);
        $this->assertEquals(message_format_message_text($dbm6), $m6->message);
        $this->assertEquals(transform::datetime($now - (4 * DAYSECS)), $m6->timecreated);
        $this->assertEquals('-', $m6->timeread);
    }

    /**
     * Test for provider::export_user_data().
     */
    public function test_export_for_context_with_notifications() {
        $this->resetAfterTest();

        // Create users to test with.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        $now = time();
        $timeread = $now - DAYSECS;

        // Send notifications from user 1 to user 2.
        $this->create_notification($user1->id, $user2->id, $now + (9 * DAYSECS), $timeread);
        $this->create_notification($user2->id, $user1->id, $now + (8 * DAYSECS));
        $this->create_notification($user1->id, $user2->id, $now + (7 * DAYSECS));

        // Send notifications from user 3 to user 1.
        $this->create_notification($user3->id, $user1->id, $now + (6 * DAYSECS), $timeread);
        $this->create_notification($user1->id, $user3->id, $now + (5 * DAYSECS));
        $this->create_notification($user3->id, $user1->id, $now + (4 * DAYSECS));

        // Send notifications from user 3 to user 2 - should not be part of the export.
        $this->create_notification($user3->id, $user2->id, $now + (3 * DAYSECS), $timeread);
        $this->create_notification($user2->id, $user3->id, $now + (2 * DAYSECS));
        $this->create_notification($user3->id, $user2->id, $now + (1 * DAYSECS));

        $this->export_context_data_for_user($user1->id, \context_system::instance(), 'core_message');

        $writer = writer::with_context(\context_system::instance());

        $this->assertTrue($writer->has_any_data());

        // Confirm the notifications.
        $notifications = (array) $writer->get_data([get_string('notifications', 'core_message')]);

        $this->assertCount(6, $notifications);
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        $this->resetAfterTest();

        // Create users to test with.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();

        $now = time();
        $timeread = $now - DAYSECS;

        $systemcontext = \context_system::instance();

        // Create contacts.
        \core_message\api::add_contact($user1->id, $user2->id);

        // Create contact requests.
        \core_message\api::create_contact_request($user1->id, $user3->id);

        // Block a user.
        \core_message\api::block_user($user1->id, $user3->id);

        // Create messages.
        $m1 = $this->create_message($user1->id, $user2->id, $now + (9 * DAYSECS), true);
        $m2 = $this->create_message($user2->id, $user1->id, $now + (8 * DAYSECS));

        // Create notifications.
        $n1 = $this->create_notification($user1->id, $user2->id, $now + (9 * DAYSECS), $timeread);
        $n2 = $this->create_notification($user2->id, $user1->id, $now + (8 * DAYSECS));

        // Delete one of the messages.
        \core_message\api::delete_message($user1->id, $m2);

        // There should be 1 contact.
        $this->assertEquals(1, $DB->count_records('message_contacts'));

        // There should be 1 contact request.
        $this->assertEquals(1, $DB->count_records('message_contact_requests'));

        // There should be 1 blocked user.
        $this->assertEquals(1, $DB->count_records('message_users_blocked'));

        // There should be two messages.
        $this->assertEquals(2, $DB->count_records('messages'));

        // There should be two user actions - one for reading the message, one for deleting.
        $this->assertEquals(2, $DB->count_records('message_user_actions'));

        // There should be two conversation members.
        $this->assertEquals(2, $DB->count_records('message_conversation_members'));

        // There should be two notifications + one for the contact request.
        $this->assertEquals(3, $DB->count_records('notifications'));

        provider::delete_data_for_all_users_in_context($systemcontext);

        // Confirm all has been deleted.
        $this->assertEquals(0, $DB->count_records('message_contacts'));
        $this->assertEquals(0, $DB->count_records('message_contact_requests'));
        $this->assertEquals(0, $DB->count_records('message_users_blocked'));
        $this->assertEquals(0, $DB->count_records('messages'));
        $this->assertEquals(0, $DB->count_records('message_user_actions'));
        $this->assertEquals(0, $DB->count_records('message_conversation_members'));
        $this->assertEquals(0, $DB->count_records('notifications'));
    }

    /**
     * Test for provider::delete_data_for_user().
     */
    public function test_delete_data_for_user() {
        global $DB;

        $this->resetAfterTest();

        // Create users to test with.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();
        $user5 = $this->getDataGenerator()->create_user();
        $user6 = $this->getDataGenerator()->create_user();

        $now = time();
        $timeread = $now - DAYSECS;

        // Create contacts.
        \core_message\api::add_contact($user1->id, $user2->id);
        \core_message\api::add_contact($user2->id, $user3->id);

        // Create contact requests.
        \core_message\api::create_contact_request($user1->id, $user3->id);
        \core_message\api::create_contact_request($user2->id, $user4->id);

        // Block users.
        \core_message\api::block_user($user1->id, $user5->id);
        \core_message\api::block_user($user2->id, $user6->id);

        // Create messages.
        $m1 = $this->create_message($user1->id, $user2->id, $now + (9 * DAYSECS), $timeread);
        $m2 = $this->create_message($user2->id, $user1->id, $now + (8 * DAYSECS));

        // Create notifications.
        $n1 = $this->create_notification($user1->id, $user2->id, $now + (9 * DAYSECS), $timeread);
        $n2 = $this->create_notification($user2->id, $user1->id, $now + (8 * DAYSECS));
        $n2 = $this->create_notification($user2->id, $user3->id, $now + (8 * DAYSECS));

        // Delete one of the messages.
        \core_message\api::delete_message($user1->id, $m2);

        // There should be 2 contacts.
        $this->assertEquals(2, $DB->count_records('message_contacts'));

        // There should be 1 contact request.
        $this->assertEquals(2, $DB->count_records('message_contact_requests'));

        // There should be 1 blocked user.
        $this->assertEquals(2, $DB->count_records('message_users_blocked'));

        // There should be two messages.
        $this->assertEquals(2, $DB->count_records('messages'));

        // There should be two user actions - one for reading the message, one for deleting.
        $this->assertEquals(2, $DB->count_records('message_user_actions'));

        // There should be two conversation members.
        $this->assertEquals(2, $DB->count_records('message_conversation_members'));

        // There should be three notifications + two for the contact requests.
        $this->assertEquals(5, $DB->count_records('notifications'));

        $systemcontext = \context_system::instance();
        $contextlist = new \core_privacy\local\request\approved_contextlist($user1, 'core_message',
            [$systemcontext->id]);
        provider::delete_data_for_user($contextlist);

        // Confirm the user 2 data still exists.
        $contacts = $DB->get_records('message_contacts');
        $contactrequests = $DB->get_records('message_contact_requests');
        $blockedusers = $DB->get_records('message_users_blocked');
        $messages = $DB->get_records('messages');
        $muas = $DB->get_records('message_user_actions');
        $mcms = $DB->get_records('message_conversation_members');
        $notifications = $DB->get_records('notifications');

        $this->assertCount(1, $contacts);
        $contact = reset($contacts);
        $this->assertEquals($user2->id, $contact->userid);
        $this->assertEquals($user3->id, $contact->contactid);

        $this->assertCount(1, $contactrequests);
        $contactrequest = reset($contactrequests);
        $this->assertEquals($user2->id, $contactrequest->userid);
        $this->assertEquals($user4->id, $contactrequest->requesteduserid);

        $this->assertCount(1, $blockedusers);
        $blockeduser = reset($blockedusers);
        $this->assertEquals($user2->id, $blockeduser->userid);
        $this->assertEquals($user6->id, $blockeduser->blockeduserid);

        $this->assertCount(1, $messages);
        $message = reset($messages);
        $this->assertEquals($m2, $message->id);

        $this->assertCount(1, $muas);
        $mua = reset($muas);
        $this->assertEquals($user2->id, $mua->userid);
        $this->assertEquals($m1, $mua->messageid);
        $this->assertEquals(\core_message\api::MESSAGE_ACTION_READ, $mua->action);

        $this->assertCount(1, $mcms);
        $mcm = reset($mcms);
        $this->assertEquals($user2->id, $mcm->userid);

        $this->assertCount(2, $notifications);
        ksort($notifications);

        $notification = array_shift($notifications);
        $this->assertEquals($user2->id, $notification->useridfrom);
        $this->assertEquals($user4->id, $notification->useridto);

        $notification = array_shift($notifications);
        $this->assertEquals($user2->id, $notification->useridfrom);
        $this->assertEquals($user3->id, $notification->useridto);
    }

    /**
     * Creates a message to be used for testing.
     *
     * @param int $useridfrom The user id from
     * @param int $useridto The user id to
     * @param int $timecreated
     * @param bool $read Do we want to mark the message as read?
     * @return int The id of the message
     * @throws dml_exception
     */
    private function create_message(int $useridfrom, int $useridto, int $timecreated = null, bool $read = false) {
        global $DB;

        static $i = 1;

        if (is_null($timecreated)) {
            $timecreated = time();
        }

        if (!$conversationid = \core_message\api::get_conversation_between_users([$useridfrom, $useridto])) {
            $conversationid = \core_message\api::create_conversation_between_users([$useridfrom,
                $useridto]);
        }

        // Ok, send the message.
        $record = new stdClass();
        $record->useridfrom = $useridfrom;
        $record->conversationid = $conversationid;
        $record->subject = 'No subject';
        $record->fullmessage = 'A rad message ' . $i;
        $record->smallmessage = 'A rad message ' . $i;
        $record->timecreated = $timecreated;

        $i++;

        $record->id = $DB->insert_record('messages', $record);

        if ($read) {
            \core_message\api::mark_message_as_read($useridto, $record);
        }

        return $record->id;
    }

    /**
     * Creates a notification to be used for testing.
     *
     * @param int $useridfrom The user id from
     * @param int $useridto The user id to
     * @param int|null $timecreated The time the notification was created
     * @param int|null $timeread The time the notification was read, null if it hasn't been.
     * @return int The id of the notification
     * @throws dml_exception
     */
    private function create_notification(int $useridfrom, int $useridto, int $timecreated = null, int $timeread = null) {
        global $DB;

        static $i = 1;

        if (is_null($timecreated)) {
            $timecreated = time();
        }

        $record = new stdClass();
        $record->useridfrom = $useridfrom;
        $record->useridto = $useridto;
        $record->subject = 'No subject';
        $record->fullmessage = 'Some rad notification ' . $i;
        $record->smallmessage = 'Yo homie, you got some stuff to do, yolo. ' . $i;
        $record->timeread = $timeread;
        $record->timecreated = $timecreated;

        $i++;

        return $DB->insert_record('notifications', $record);
    }

    /**
     * Comparison function for sorting messages.
     *
     * @param   \stdClass $a
     * @param   \stdClass $b
     * @return  bool
     */
    protected static function sort_messages($a, $b) {
        return $a->message > $b->message;
    }

    /**
     * Comparison function for sorting contacts.
     *
     * @param   \stdClass $a
     * @param   \stdClass $b
     * @return  bool
     */
    protected static function sort_contacts($a, $b) {
        return $a->contact > $b->contact;
    }
}
