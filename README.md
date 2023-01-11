Use clearskyevents.php to list all events with clear weather(for testing set to rain, because there is no clear weather
in Estonia)

Use registration.php to add new registration
To use registration, send POST request with json in format:
{
    "name":"John Doe",
    "email":"johndoe@gmail.com",
    "town": "City name",
    "dateandtime": "YYYY-MM-DD HH-MM-SS",
    "comment": "Your comment"
}

Use listregistrations.php to get all registrations
