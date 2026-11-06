# PHP secure coding / CSRF exercises

Small PHP exercises I wrote at the ITS course while learning secure coding: login with sessions, CSRF tokens, soft delete, roles, and a token-protected JSON API. Two of them come with a small attack page that submits a form from another site, to see what a CSRF token does and does not stop. Folders are numbered in the order I would read them.

| Folder | What it is | Approx. date | What I was learning |
|---|---|---|---|
| [01-app-login](01-app-login) | Login, dashboard with a message list, add and soft-delete messages, roles (author / editor / admin), plus an attack page | Sep 2025 | Sessions, `password_hash`, CSRF tokens in forms, soft delete, a first look at how a CSRF attack works |
| [02-bacheca](02-bacheca) | Message board: login, post a message, delete with a token, roles, plus an attack page | Sep 2025 (messages.php touched again Apr 2026) | CSRF tokens on every form, soft delete with a far-future "not deleted" date |
| [03-messages-api](03-messages-api) | JSON API for the same board: login returns a signed token, other calls need `Authorization: Bearer <token>` | Nov 2025 | HMAC signed tokens with an expiry, JSON in and out, API instead of HTML forms |

## Running them

Plain PHP + MySQL, written for MAMP. Copy the repo into `htdocs` (keep the folder names, the attack pages point at `http://localhost:8888/<folder>/...`) and create the database with the `schema.sql` of the folder you want to try. The schema files are rebuilt from the queries in the code, the original dumps are not here. Folders 02 and 03 use the same database name (`bacheca`) with different tables, so use one at a time. The connection uses the MAMP defaults (the default MAMP MySQL user), change them in the code if yours are different.

For 03 copy `config.example.php` to `config.php` and put your own secret key in it.

The attack pages (`csrf_attack_demo.*`) submit a form to the app as soon as they are opened. Log in to the app first, then open the page from a different origin (for example `127.0.0.1` instead of `localhost`) to see whether the message gets through. In 01 the insert page never checks the token, so it should work; in 02 the token check rejects it. Recent browsers treat session cookies as SameSite=Lax by default, so a cross-site POST may not even carry the session, depending on the browser.

## Known issues

These are learning exercises, I left the beginner code as it was.

- 01: `insert.php` does not check the CSRF token even though the form sends one, so the attack page works. `index.php` shows the messages and has a delete form without any login. There is no `logout` check on the token either.
- 02: `delete.php` only checks the token, not whether the message is yours or you are an admin. The role names (`autor`, `admin`) are matched as plain strings.
- 03: no rate limit on login, the `Authorization` header parsing is very basic, and database error messages are sent back to the client. The API cannot delete messages.
- Database connection details are written directly in the code (local MAMP defaults).
- `01-app-login/dbconnector.php` and the other connector classes open a new PDO connection on every `prepare()` call. It works, it is just not how I would do it now.
