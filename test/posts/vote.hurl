# Setup
POST http://localhost:8080/api/auth/login.php
{
  "username": "test",
  "password": "123456"
}
HTTP 200

# Setup 2
POST http://localhost:8080/api/posts/new.php
{
  "parent": null,
  "content": "/api/posts/vote.php test setup"
}
HTTP 200
[Captures]
parent: jsonpath "$.id"

# Parent is not a valid UUID
POST http://localhost:8080/api/posts/vote.php
{
  "post": "oops",
  "stance": "up"
}
HTTP 400
[Asserts]
body matches /Voto/

# Post does not exist
POST http://localhost:8080/api/posts/vote.php
{
  "post": "00000000-0000-0000-0000-000000000000",
  "stance": "up"
}
HTTP 400
[Asserts]
body matches /no existe/

# Invalid stance
POST http://localhost:8080/api/posts/vote.php
{
  "post": "{{parent}}",
  "stance": "idk"
}
HTTP 400
[Asserts]
body matches /Voto/

# Should work
POST http://localhost:8080/api/posts/vote.php
{
  "post": "{{parent}}",
  "stance": "none"
}
HTTP 200

# Should work
POST http://localhost:8080/api/posts/vote.php
{
  "post": "{{parent}}",
  "stance": "up"
}
HTTP 200

# Should work
POST http://localhost:8080/api/posts/vote.php
{
  "post": "{{parent}}",
  "stance": "down"
}
HTTP 200
