<?php
// Healthcheck endpoint for Railway — bypasses Laravel entirely.
// No database, no session, no middleware required.
http_response_code(200);
echo 'OK';
