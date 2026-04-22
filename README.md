# Quety - Talking Stick App

A simple PHP-based web application for managing speaking turns in meetings using the "talking stick" methodology.

## Features

- **User Session Management**: Users can join meetings by entering their name
- **Queue System**: Users can queue up to speak and see their position in the queue
- **Live Voting**: Participants can vote Yes/No on current discussions
- **Remarks**: Users can add remarks that are visible to all participants
- **Real-time Updates**: UI automatically refreshes every 2 seconds to show latest state
- **Admin Panel**: Administrator can reset/clear all meeting data
- **Leave Meeting**: Users can cleanly leave the meeting (removes from queue and clears remarks)

## Files Structure

```
├── index.php      # Main application interface
├── api.php        # Backend API handling all actions
├── admin.php      # Admin panel for resetting meeting data
├── data.json      # Persistent storage for queue, votes, and remarks
└── README.md      # This file
```

## Requirements

- PHP 7.0 or higher
- A web server (Apache, Nginx, or PHP built-in server)
- Write permissions for `data.json`

## Installation

1. Clone or download this repository to your web server directory
2. Ensure the web server has write permissions for the directory (to create/update `data.json`)
3. Access the application through your web browser

### Using PHP Built-in Server

```bash
cd /path/to/quety
php -S localhost:8000
```

Then open http://localhost:8000 in your browser.

## Usage

### For Participants

1. **Join Meeting**: Enter your name on the welcome screen and click "Join Meeting"
2. **Queue to Speak**: Click the "Queue" button to add yourself to the speaking queue
3. **Vote**: Use the Yes/No buttons to vote on current discussions
4. **Add Remarks**: Type remarks in the input field and click "Save/Clear Remark"
5. **Leave Meeting**: Click "Leave Meeting" to cleanly exit (removes you from queue and clears your remarks)

### For Administrators

1. Navigate to `admin.php` (link available at the bottom of the main page)
2. Click "CLEAR ALL DATA (RESET)" to reset the queue, votes, and remarks

## API Endpoints

All API calls are handled through `api.php` with query parameters:

| Action | Parameters | Description |
|--------|------------|-------------|
| `status` | - | Get current meeting state (queue, votes, remarks) |
| `toggleQueue` | - | Add/remove current user from speaking queue |
| `vote` | `type` (yes/no), `mode` (add/remove) | Cast or remove a vote |
| `remark` | POST: `remark` | Set or clear user's remark |
| `reset` | - | Clear all meeting data (admin only) |
| `leave` | - | Remove user from queue and clear their remark |

## Data Storage

Meeting state is persisted in `data.json`:

```json
{
  "queue": [],
  "yesCount": 0,
  "noCount": 0,
  "remarks": {},
  "votes": {}
}
```

## Security Notes

- User input is sanitized using `htmlspecialchars()` to prevent XSS attacks
- Session-based authentication for user identification
- No database required - uses JSON file storage

## License

This project is open source and available for use.
