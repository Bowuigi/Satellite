# Should work
GET http://localhost:8080/api/auth/me.php
HTTP 200
[Asserts]
jsonpath "$.logged_in" == false

# Setup
POST http://localhost:8080/api/auth/login.php
{
  "username": "test",
  "password": "123456"
}
HTTP 200

# Should work
GET http://localhost:8080/api/auth/me.php
HTTP 200
[Asserts]
jsonpath "$.logged_in" == true
jsonpath "$.name" == "test"
jsonpath "$.filter_amount" >= 0
jsonpath "$.filters" exists
