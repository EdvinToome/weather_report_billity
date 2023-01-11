

Use clearskyevents.php to list all events with clear weather(for testing set to rain, because there is no clear weather
in Estonia)
Test here: https://enos.itcollege.ee/~edtoom/billity/clearskyevents.php

Use registration.php to add new registration
To use registration, send POST request with json in format:
{
    "name":"John Doe",
    "email":"johndoe@gmail.com",
    "town": "City name",
    "dateandtime": "YYYY-MM-DD HH-MM-SS",
    "comment": "Your comment"
}
Send request here: https://enos.itcollege.ee/~edtoom/billity/registration.php
Sends back JSON with bool success, status code and message


Use listregistrations.php to get all registrations
Test here: https://enos.itcollege.ee/~edtoom/billity/listregistrations.php
