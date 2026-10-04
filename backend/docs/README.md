# API guide

- [OpenAPI 3.0 specification](openapi.json)
- [Postman collection](postman/ProjectManagement.postman_collection.json)
- [Local Postman environment](postman/Local.postman_environment.json)

## OpenAPI

Import `openapi.json` into Swagger Editor, Swagger UI, or Postman to explore the endpoints and schemas. The default server is `http://localhost:8000/api`; `http://localhost:3000/api` uses the frontend proxy.

Call `POST /login` with an account's email and password. Use the returned token as `Authorization: Bearer <token>` on subsequent requests. Tokens expire after 12 hours. Send `Accept: application/json` and use JSON request bodies.

## Postman

1. Import the collection and local environment, then select that environment.
2. Set `email` and `password` to an existing account. Seeded demo accounts are defined in [DatabaseSeeder.php](../database/seeders/DatabaseSeeder.php).
3. Run **Authentication → Login**. The response script saves `token` automatically.
4. Set record IDs to existing records, or create a user, client, project, then task. Successful creation requests save their IDs automatically. Set `new_user_password` to a password of 12–128 characters before creating a user.
5. Use list filters, detail requests, updates, comments, and reports as needed. Logout clears the saved token after revocation.

The supplied environment contains no credentials or tokens. Keep populated environment exports private. Create requests require an admin or manager; user management requires an admin. Staff may update only the status of their assigned tasks within member projects.

Run requests individually: the collection includes write and delete operations. A complete collection run changes the selected database. To remove example records, delete the task, project, client, then user; clients with projects cannot be deleted.

## Responses

Resource responses use `{ "data": ... }`. Lists include `data`, `links`, and `meta`. Login, current-user, and dashboard responses are unwrapped. Relationship and count fields appear when loaded by the endpoint.

Creation returns `201`; deletion and logout return `204`. Errors use a `message`; validation errors can also include field `errors`. Authentication failures return `401`, permission failures `403`, missing records `404`, invalid input or business-rule failures `422`, and login throttling `429`.

Update both reference files when changing routes, validation, or response resources. PUT and PATCH both accept partial updates.
