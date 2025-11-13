# Only GET requests are allowed
POST http://localhost:8080/api/filters/view.php
HTTP 400
[Asserts]
body matches /GET/

# Lacks parameter "user"
GET http://localhost:8080/api/filters/view.php?filter=nonexistent
HTTP 400
[Asserts]
body matches /user/

# Lacks parameter "filter"
GET http://localhost:8080/api/filters/view.php?user=test
HTTP 400
[Asserts]
body matches /filter/

# Filter does not exist
GET http://localhost:8080/api/filters/view.php?user=test&filter=nonexistent
HTTP 400
[Asserts]
body matches /existe/

# Should work
GET http://localhost:8080/api/filters/view.php?user=test&filter=trending
HTTP 200
[Asserts]
jsonpath "$.name" matches /^[a-z0-9\-_\.]{1,50}$/
jsonpath "$.author" matches /^[a-z0-9\-_\.]{1,50}$/
jsonpath "$.condition[*]" count == 1
jsonpath "$.sort_by" == "upvotes"

# Should work
GET http://localhost:8080/api/filters/view.php?user=test&filter=none
HTTP 200
[Asserts]
jsonpath "$.name" matches /^[a-z0-9\-_\.]{1,50}$/
jsonpath "$.author" matches /^[a-z0-9\-_\.]{1,50}$/
jsonpath "$.condition[*]" count == 0
jsonpath "$.sort_by" == "latest"

# Should work
GET http://localhost:8080/api/filters/view.php?user=test&filter=my-posts
HTTP 200
[Asserts]
jsonpath "$.name" matches /^[a-z0-9\-_\.]{1,50}$/
jsonpath "$.author" matches /^[a-z0-9\-_\.]{1,50}$/
jsonpath "$.condition[*]" count == 1
jsonpath "$.condition[0].author" == "test"
jsonpath "$.sort_by" == "latest"
