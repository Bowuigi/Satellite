# The request body is empty
POST http://localhost:8080/api/auth/login.php
HTTP 400
[Asserts]
body matches /JSON/

# It lacks required parameters
POST http://localhost:8080/api/auth/login.php
{
  "username": "test"
}
HTTP 400
[Asserts]
body matches /JSON/

# Validation fails
POST http://localhost:8080/api/auth/login.php
{
  "username": "test",
  "password": "123"
}
HTTP 400
[Asserts]
body matches /Credenciales/

# User not found
POST http://localhost:8080/api/auth/login.php
{
  "username": "nonexistent",
  "password": "123456"
}
HTTP 400
[Asserts]
body matches /no encontrado/

# Should work
POST http://localhost:8080/api/auth/login.php
{
  "username": "test",
  "password": "123456"
}
HTTP 200

# Should work even though the user is already logged in
POST http://localhost:8080/api/auth/login.php
{
  "username": "test",
  "password": "123456"
}
HTTP 200
