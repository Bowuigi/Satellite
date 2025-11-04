GET http://localhost:8080/api/users/list.php
HTTP 200
[Asserts]
jsonpath "$.amount" >= 1
jsonpath "$.results" contains "test"
