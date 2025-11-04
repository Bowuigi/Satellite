# Setup
POST http://localhost:8080/api/auth/login.php
{
  "username": "test",
  "password": "123456"
}
HTTP 200

# Should work
POST http://localhost:8080/api/auth/logout.php
HTTP 200

# Not logged in
POST http://localhost:8080/api/auth/logout.php
HTTP 400
[Asserts]
body matches /registrado/
