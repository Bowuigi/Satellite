# The request body is empty
POST http://localhost:8080/api/auth/signup.php
HTTP 400
[Asserts]
body matches /JSON/

# It lacks required parameters
POST http://localhost:8080/api/auth/signup.php
{
  "username": "test"
}
HTTP 400
[Asserts]
body matches /JSON/

# Validation fails
POST http://localhost:8080/api/auth/signup.php
{
  "username": "test",
  "password": "123"
}
HTTP 400
[Asserts]
body matches /Credenciales/

# Should work
POST http://localhost:8080/api/auth/signup.php
{
  "username": "test",
  "password": "123456"
}
HTTP 200

# The user already exists
POST http://localhost:8080/api/auth/signup.php
{
  "username": "test",
  "password": "123456"
}
HTTP 400
[Asserts]
body matches /ya existe/
