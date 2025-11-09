# Not logged in
POST http://localhost:8080/api/filters/remove.php
HTTP 400
[Asserts]
body matches /registrado/

# Setup
POST http://localhost:8080/api/auth/login.php
{
  "username": "test",
  "password": "123456"
}
HTTP 200

# Only POST requests are allowed
GET http://localhost:8080/api/filters/remove.php
HTTP 400
[Asserts]
body matches /POST/

# Filter does not exist
POST http://localhost:8080/api/filters/remove.php
{
  "name": "nonexistent"
}
HTTP 400
[Asserts]
body matches /existe/

# Should work
POST http://localhost:8080/api/filters/remove.php
{
  "name": "trending"
}
HTTP 200
