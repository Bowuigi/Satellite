# User not found
GET http://localhost:8080/api/users/view.php?name=nonexistent
HTTP 404
[Asserts]
body matches /Usuario/

# Should work
GET http://localhost:8080/api/users/view.php?name=test
HTTP 200
[Asserts]
jsonpath "$.name" == "test"
jsonpath "$.joined_at" matches /\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/
jsonpath "$.default_filter" exists
jsonpath "$.filter_amount" >= 0
jsonpath "$.filters" != null
