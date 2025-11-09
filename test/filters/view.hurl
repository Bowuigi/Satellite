# Not logged in
GET http://localhost:8080/api/filters/view.php
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

# Only GET requests are allowed
POST http://localhost:8080/api/filters/view.php
HTTP 400
[Asserts]
body matches /GET/

# Filter does not exist
GET http://localhost:8080/api/filters/view.php?name=nonexistent
HTTP 400
[Asserts]
body matches /existe/

# Should work
GET http://localhost:8080/api/filters/view.php?name=trending
HTTP 200
[Asserts]
jsonpath "$.name" matches /^[a-z0-9\-_\.]{1,50}$/
jsonpath "$.author" matches /^[a-z0-9\-_\.]{1,50}$/
jsonpath "$.condition[*]" count == 1
jsonpath "$.sort_by" == "upvotes"

# Should work
GET http://localhost:8080/api/filters/view.php?name=none
HTTP 200
[Asserts]
jsonpath "$.name" matches /^[a-z0-9\-_\.]{1,50}$/
jsonpath "$.author" matches /^[a-z0-9\-_\.]{1,50}$/
jsonpath "$.condition[*]" count == 0
jsonpath "$.sort_by" == "latest"

# Should work
GET http://localhost:8080/api/filters/view.php?name=my-posts
HTTP 200
[Asserts]
jsonpath "$.name" matches /^[a-z0-9\-_\.]{1,50}$/
jsonpath "$.author" matches /^[a-z0-9\-_\.]{1,50}$/
jsonpath "$.condition[*]" count == 1
jsonpath "$.condition[0].author" == "test"
jsonpath "$.sort_by" == "latest"
