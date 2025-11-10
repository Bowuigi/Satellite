# Wrong method
POST http://localhost:8080/api/view.php
HTTP 400
[Asserts]
body matches /GET/

# Filter name is not an identifier
GET http://localhost:8080/api/view.php?filter=%00
HTTP 400
[Asserts]
body matches /filtro inválido/

# Filters are not allowed without being logged in
GET http://localhost:8080/api/view.php?filter=trending
HTTP 400
[Asserts]
body matches /invitado/

# Impossible UUID, continues silently
GET http://localhost:8080/api/view.php?parent=00000000-0000-0000-0000-000000000000
HTTP 200
[Asserts]
jsonpath "$.parent_id" matches /^[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}/
jsonpath "$.amount" == 0
jsonpath "$.children[*]" count == 0

GET http://localhost:8080/api/view.php
HTTP 200
[Asserts]
jsonpath "$.amount" > 0
jsonpath "$.parent_id" == null
jsonpath "$.children[*].id" count > 0
jsonpath "$.children[0].id" matches /^[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}/
jsonpath "$.children[*].posted_at" count > 0
jsonpath "$.children[0].posted_at" matches /\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/
jsonpath "$.children[*].author" count > 0
jsonpath "$.children[0].author" matches /^[a-z0-9\-_\.]{1,50}$/
jsonpath "$.children[*].content" count > 0
jsonpath "$.children[0].content" != null
jsonpath "$.children[*].score" count > 0
jsonpath "$.children[*].stance" count > 0
jsonpath "$.children[0].stance" matches /up|down|none/

# Setup
POST http://localhost:8080/api/auth/login.php
{
  "username": "test",
  "password": "123456"
}
HTTP 200

# Should work (now as a logged in user)
GET http://localhost:8080/api/view.php
HTTP 200
[Asserts]
jsonpath "$.amount" > 0
jsonpath "$.parent_id" == null
jsonpath "$.children[*].id" count > 0
jsonpath "$.children[0].id" matches /^[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}/
jsonpath "$.children[*].posted_at" count > 0
jsonpath "$.children[0].posted_at" matches /\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/
jsonpath "$.children[*].author" count > 0
jsonpath "$.children[0].author" matches /^[a-z0-9\-_\.]{1,50}$/
jsonpath "$.children[*].content" count > 0
jsonpath "$.children[0].content" != null
jsonpath "$.children[*].score" count > 0
jsonpath "$.children[*].stance" count > 0
jsonpath "$.children[0].stance" matches /up|down|none/

# Should work (with a filter, "none" disallows any posts)
GET http://localhost:8080/api/view.php?filter=none
HTTP 200
[Asserts]
jsonpath "$.amount" == 0
jsonpath "$.children[*]" count == 0

# Should work (with a filter that should give results)
GET http://localhost:8080/api/view.php?filter=my-posts
HTTP 200
[Asserts]
jsonpath "$.amount" > 0
jsonpath "$.parent_id" == null
jsonpath "$.children[*].id" count > 0
jsonpath "$.children[0].id" matches /^[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}/
jsonpath "$.children[*].posted_at" count > 0
jsonpath "$.children[0].posted_at" matches /\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/
jsonpath "$.children[*].author" count > 0
jsonpath "$.children[0].author" matches /^[a-z0-9\-_\.]{1,50}$/
jsonpath "$.children[*].content" count > 0
jsonpath "$.children[0].content" != null
jsonpath "$.children[*].score" count > 0
jsonpath "$.children[*].stance" count > 0
jsonpath "$.children[0].stance" matches /up|down|none/
