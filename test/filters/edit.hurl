# Not logged in
POST http://localhost:8080/api/filters/edit.php
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
GET http://localhost:8080/api/filters/edit.php
HTTP 400
[Asserts]
body matches /POST/

# "condition" is an array of objects
POST http://localhost:8080/api/filters/edit.php
{
  "name": "error",
  "condition": {"author": "test"},
  "sort_by": "upvotes"
}
HTTP 400
[Asserts]
body matches /JSON/

# Invalid sort order
POST http://localhost:8080/api/filters/edit.php
{
  "name": "error",
  "condition": [],
  "sort_by": "do-not"
}
HTTP 400
[Asserts]
body matches /JSON/

# Unknown condition "error"
POST http://localhost:8080/api/filters/edit.php
{
  "name": "error",
  "condition": [{"error": "not found"}],
  "sort_by": "upvotes"
}
HTTP 400
[Asserts]
body matches /JSON/

# Should work
POST http://localhost:8080/api/filters/edit.php
{
  "name": "trending",
  "condition": [[]],
  "sort_by": "upvotes"
}
HTTP 200
[Asserts]
jsonpath "$.id" matches /^[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}/

# Should work
POST http://localhost:8080/api/filters/edit.php
{
  "name": "none",
  "condition": [],
  "sort_by": "latest"
}
HTTP 200
[Asserts]
jsonpath "$.id" matches /^[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}/

# Should work
POST http://localhost:8080/api/filters/edit.php
{
  "name": "my-posts",
  "condition": [{"author": "test"}],
  "sort_by": "latest"
}
HTTP 200
[Asserts]
jsonpath "$.id" matches /^[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}/

# Filter already exists
POST http://localhost:8080/api/filters/edit.php
{
  "name": "my-posts",
  "condition": [{"author": "test"}],
  "sort_by": "latest"
}
HTTP 400
[Asserts]
body matches /existe/
