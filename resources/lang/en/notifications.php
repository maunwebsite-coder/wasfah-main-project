<?php

return [
    'page' => [
        'title' => 'Notifications',
        'description' => 'All your notifications in one place',
        'empty_title' => 'No notifications available',
        'empty_description' => 'Your notifications will appear here when they are ready.',
    ],
    'buttons' => [
        'mark_all_read' => 'Mark all as read',
        'clear_read' => 'Clear read',
        'cancel' => 'Cancel',
    ],
    'tooltips' => [
        'mark_as_read' => 'Mark as read',
        'delete' => 'Delete notification',
    ],
    'modals' => [
        'delete_single' => [
            'title' => 'Delete notification',
            'message' => 'Are you sure you want to delete this notification?',
            'note' => 'This action cannot be undone.',
            'confirm' => 'Delete',
        ],
        'mark_all' => [
            'title' => 'Mark all as read',
            'message' => 'Do you want to mark all notifications as read?',
            'note' => 'All unread notifications will be marked as read.',
            'confirm' => 'Mark all',
        ],
        'clear_read' => [
            'title' => 'Delete read notifications',
            'message' => 'Are you sure you want to delete all read notifications?',
            'note' => 'All previously read notifications will be deleted and this cannot be undone.',
            'confirm' => 'Delete read',
        ],
    ],
    'status' => [
        'deleting' => 'Deleting...',
        'updating' => 'Updating...',
    ],
    'messages' => [
        'delete_success' => 'Notification deleted successfully!',
        'delete_error' => 'An error occurred while deleting the notification',
        'mark_all_success' => 'All notifications marked as read!',
        'mark_all_error' => 'An error occurred while updating notifications',
        'clear_read_success' => 'Read notifications deleted successfully!',
        'clear_read_error' => 'An error occurred while deleting notifications',
    ],
];
