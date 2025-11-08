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

# Setup part 2
POST http://localhost:8080/api/filters/edit.php
{
  "name": "trending",
  "condition": [[]],
  "sort_by": "upvotes"
}
HTTP 200
[Captures]
filter: jsonpath "$.id"

# Only POST requests are allowed
GET http://localhost:8080/api/filters/remove.php
HTTP 400
[Asserts]
body matches /POST/

# Filter does not exist
POST http://localhost:8080/api/filters/remove.php
{
  "id": "00000000-0000-0000-0000-000000000000"
}
HTTP 400
[Asserts]
body matches /existe/

# Should work
POST http://localhost:8080/api/filters/remove.php
{
  "id": "{{filter}}"
}
HTTP 200
