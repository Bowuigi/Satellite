# Setup
POST http://localhost:8080/api/auth/login.php
{
  "username": "test",
  "password": "123456"
}
HTTP 200

# Parent is not a valid UUID
POST http://localhost:8080/api/posts/new.php
{
  "parent": "oops",
  "content": "Hello from the test suite!"
}
HTTP 400
[Asserts]
body matches /Publicación/

# Content is too short
POST http://localhost:8080/api/posts/new.php
{
  "parent": null,
  "content": ""
}
HTTP 400
[Asserts]
body matches /Publicación inválida$/

# Parent post does not exist
# The given UUID will never be generated (it lacks the version)
POST http://localhost:8080/api/posts/new.php
{
  "parent": "00000000-0000-0000-0000-000000000000",
  "content": "This should not be visible in the database!"
}
HTTP 400
[Asserts]
body matches /padre no existe/

# Should work
POST http://localhost:8080/api/posts/new.php
{
  "parent": null,
  "content": "Hello from the test suite!"
}
HTTP 200
